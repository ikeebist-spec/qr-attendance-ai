window.isChatOpen = false;
window.chatMessages = [{ sender: 'bot', text: 'Hello! I am the ESSU CCS AI Assistant. How can I help you manage attendance today?' }];

window.renderChatMessages = function () {
    const container = document.getElementById('chat-messages');
    if (!container) return;
    container.innerHTML = window.chatMessages.map(msg => `
        <div class="flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}">
            <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm ${msg.sender === 'user'
            ? 'bg-purple-600 text-white rounded-br-none'
            : 'bg-white text-gray-800 border border-gray-200 shadow-sm rounded-bl-none'
        }">${window.escapeHTML(msg.text)}</div>
        </div>
    `).join('');
    container.scrollTop = container.scrollHeight;
};

window.runAIAnalysis = async function () {
    // 1. Fetch exact total fines based on the new backend calculation
    // Since absences are now accurately calculated per-event, we request the latest dashboard data
    let totalFines = 0;
    try {
        const dashRes = await fetch('/api/dashboard');
        if (dashRes.ok) {
            const dData = await dashRes.json();
            totalFines = dData.total_fines || 0;
        }
    } catch (e) { }

    // 2. Estimate individual student fines directly based on attendance
    // (A perfectly accurate student fine requires a joining query, but for the UI snapshot, we use the average event fine)
    const avgFine = window.events && window.events.length
        ? window.events.reduce((sum, ev) => sum + (ev.fine || 50), 0) / window.events.length
        : 50;

    const yearAndSectionAbsences = {};
    const riskList = [];

    window.students.forEach(s => {
        let fine = 0;
        if (s.absences > 0) fine = s.absences * avgFine;

        if (!yearAndSectionAbsences[s.year_and_section]) yearAndSectionAbsences[s.year_and_section] = { total: 0, count: 0 };
        yearAndSectionAbsences[s.year_and_section].total += s.absences;
        yearAndSectionAbsences[s.year_and_section].count += 1;

        if (s.absences >= 2) riskList.push({ ...s, fine: fine.toFixed(2) });
    });

    riskList.sort((a, b) => b.absences - a.absences);

    const yearAndSectionRisk = Object.keys(yearAndSectionAbsences).map(sec => {
        const avg = yearAndSectionAbsences[sec].total / yearAndSectionAbsences[sec].count;
        return { year_and_section: sec, averageAbsences: avg.toFixed(2) };
    }).filter(s => s.averageAbsences > 0).sort((a, b) => b.averageAbsences - a.averageAbsences);

    const warnings = [];
    if (yearAndSectionRisk.length > 0 && yearAndSectionRisk[0].averageAbsences >= 2)
        warnings.push(`Predictive Alert: Year and Section ${yearAndSectionRisk[0].year_and_section} shows severe absenteeism. Recommend FCO intervention.`);
    if (riskList.length > 5)
        warnings.push('Systematic Risk: High volume of students with multiple absences.');
    if (warnings.length === 0)
        warnings.push('Attendance trends are currently stable.');

    window.insights = { totalFines, patternWarnings: warnings, atRiskStudents: riskList.slice(0, 5), atRiskYearAndSections: yearAndSectionRisk.slice(0, 3) };

    // DOM updates
    const setEl = (id, v) => { const el = document.getElementById(id); if (el) el.innerText = v; };
    setEl('dash-fine', `₱${totalFines}`);
    setEl('dash-risk', riskList.length);
    setEl('ai-total-fines', `₱${totalFines}`);

    const preds = document.getElementById('ai-predictions-list');
    if (preds) preds.innerHTML = warnings.map(w => `
        <div class="p-4 bg-orange-50 border border-orange-100 rounded-lg flex items-start text-sm text-orange-800">
            <i data-lucide="shield-alert" class="mr-3 mt-0.5 w-[18px] h-[18px] flex-shrink-0"></i><p>${window.escapeHTML(w)}</p>
        </div>`).join('');

    // Chart.js implementation for Section Risk
    const ctx = document.getElementById('year-and-section-risk-chart');
    if (ctx) {
        // Destroy existing chart if it exists to prevent overlap when re-running analysis
        if (window.sectionChartInstance) {
            window.sectionChartInstance.destroy();
        }

        if (yearAndSectionRisk.length > 0) {
            ctx.classList.remove('hidden');
            window.sectionChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: yearAndSectionRisk.map(s => s.year_and_section),
                    datasets: [{
                        label: 'Average Absences per Student',
                        data: yearAndSectionRisk.map(s => s.averageAbsences),
                        backgroundColor: 'rgba(59, 130, 246, 0.5)', // blue-500
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        } else {
            // Hide canvas and show fallback text if no absences
            ctx.classList.add('hidden');
            const fallback = document.getElementById('ai-risk-sections-list');
            if (fallback) {
                fallback.classList.remove('hidden');
                fallback.innerHTML = '<p class="text-sm text-gray-500 mt-4 text-center">No absences recorded yet.</p>';
            }
        }
    }

    const stuEl = document.getElementById('ai-risk-students-body');
    if (stuEl) stuEl.innerHTML = riskList.slice(0, 5).map(s => `
        <tr class="border-b border-gray-50">
            <td class="px-6 py-3 font-medium">${window.escapeHTML(s.student_id)}</td>
            <td class="px-6 py-3">${window.escapeHTML(s.name)}</td>
            <td class="px-6 py-3">${window.escapeHTML(s.year_and_section)}</td>
            <td class="px-6 py-3 font-bold text-red-600">${s.absences}</td>
            <td class="px-6 py-3 font-bold text-gray-800">₱${s.fine}</td>
        </tr>`).join('');

    if (typeof lucide !== 'undefined') lucide.createIcons();
};

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('chatbot-fab')?.addEventListener('click', () => {
        window.isChatOpen = !window.isChatOpen;
        document.getElementById('chatbot-window').classList.toggle('hidden');
        document.querySelector('.fab-icon-open').classList.toggle('hidden');
        document.querySelector('.fab-icon-close').classList.toggle('hidden');
        if (window.isChatOpen) { window.renderChatMessages(); document.getElementById('chat-input').focus(); }
    });
    document.getElementById('close-chat')?.addEventListener('click', () => document.getElementById('chatbot-fab').click());
    document.getElementById('chat-input')?.addEventListener('input', (e) => { document.getElementById('chat-send').disabled = e.target.value.trim() === ''; });
    document.getElementById('chat-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const userMsg = input.value.trim();
        if (!userMsg) return;
        window.chatMessages.push({ sender: 'user', text: userMsg });
        input.value = '';
        document.getElementById('chat-send').disabled = true;
        window.renderChatMessages();
        setTimeout(() => {
            const l = userMsg.toLowerCase();
            let reply = "I'm still learning! Ask me about students, QR scanning, fine computation, or events.";

            // Dynamic data integration into chatbot
            const tFines = window.insights ? window.insights.totalFines.toFixed(2) : 0;
            const tRisk = window.insights && window.insights.atRiskStudents ? window.insights.atRiskStudents.length : 0;
            const tEvents = window.events ? window.events.length : 0;
            const tStudents = window.students ? window.students.length : 0;

            if (l.includes('fine') || l.includes('compute') || l.includes('how much') || l.includes('total fine')) {
                reply = `The total accumulated fines for all students is currently ₱${tFines}. Fines are dynamically computed based on event absence records multiplied by the average event fine.`;
            } else if (l.includes('student') || l.includes('add') || l.includes('qr') || l.includes('masterlist')) {
                reply = `We currently have ${tStudents} students registered in the Student Masterlist. Each student has a unique QR code for attendance!`;
            } else if (l.includes('event') || l.includes('how many event')) {
                reply = `There are currently ${tEvents} events recorded in the system. Make sure you select the correct event before scanning!`;
            } else if (l.includes('hello') || l.includes('hi')) {
                reply = "Hello Officer! How can I assist you with the attendance system today?";
            } else if (l.includes('ai') || l.includes('risk') || l.includes('warn') || l.includes('absent')) {
                const tr = window.insights && window.insights.atRiskStudents ? window.insights.atRiskStudents.length : 0;
                reply = `According to my analysis, there are ${tr} students at high risk (2+ absences). I've highlighted them in the AI Analytics dashboard.`;
            } else if (l.includes('section')) {
                const trSection = window.insights && window.insights.atRiskYearAndSections && window.insights.atRiskYearAndSections.length > 0
                    ? window.insights.atRiskYearAndSections[0].year_and_section : 'None currently';
                reply = `The highest risk Year and Section based on absenteeism right now is ${trSection}.`;
            }

            window.chatMessages.push({ sender: 'bot', text: reply });
            window.renderChatMessages();
        }, 600);
    });
});
