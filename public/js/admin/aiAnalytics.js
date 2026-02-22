window.isChatOpen = false;
window.chatMessages = [{ sender: 'bot', text: 'Hello! I am the ESSU CCS AI Assistant. How can I help you manage attendance today?' }];

window.renderChatMessages = function () {
    const container = document.getElementById('chat-messages');
    if (!container) return;
    container.innerHTML = window.chatMessages.map(msg => `
        <div class="flex ${msg.sender === 'user' ? 'justify-end' : 'justify-start'}">
            <div class="max-w-[80%] rounded-2xl px-4 py-2 text-sm ${msg.sender === 'user'
            ? 'bg-indigo-600 text-white rounded-br-none'
            : 'bg-white text-gray-800 border border-gray-200 shadow-sm rounded-bl-none'
        }">${msg.text}</div>
        </div>
    `).join('');
    container.scrollTop = container.scrollHeight;
};

window.runAIAnalysis = function () {
    let totalFines = 0;
    const sectionAbsences = {};
    const riskList = [];

    window.students.forEach(s => {
        let fine = 0;
        if (s.absences === 1) fine = 50;
        else if (s.absences === 2) fine = 150;
        else if (s.absences >= 3) fine = 350;
        totalFines += fine;

        if (!sectionAbsences[s.section]) sectionAbsences[s.section] = { total: 0, count: 0 };
        sectionAbsences[s.section].total += s.absences;
        sectionAbsences[s.section].count += 1;

        if (s.absences >= 2) riskList.push({ ...s, fine });
    });

    riskList.sort((a, b) => b.absences - a.absences);

    const sectionRisk = Object.keys(sectionAbsences).map(sec => {
        const avg = sectionAbsences[sec].total / sectionAbsences[sec].count;
        return { section: sec, averageAbsences: avg.toFixed(2) };
    }).filter(s => s.averageAbsences > 0).sort((a, b) => b.averageAbsences - a.averageAbsences);

    const warnings = [];
    if (sectionRisk.length > 0 && sectionRisk[0].averageAbsences >= 2)
        warnings.push(`Predictive Alert: Section ${sectionRisk[0].section} shows severe absenteeism. Recommend FCO intervention.`);
    if (riskList.length > 5)
        warnings.push('Systematic Risk: High volume of students with multiple absences.');
    if (warnings.length === 0)
        warnings.push('Attendance trends are currently stable.');

    window.insights = { totalFines, patternWarnings: warnings, atRiskStudents: riskList.slice(0, 5), atRiskSections: sectionRisk.slice(0, 3) };

    // DOM updates
    const setEl = (id, v) => { const el = document.getElementById(id); if (el) el.innerText = v; };
    setEl('dash-fine', `₱${totalFines}`);
    setEl('dash-risk', riskList.length);
    setEl('ai-total-fines', `₱${totalFines}`);

    const preds = document.getElementById('ai-predictions-list');
    if (preds) preds.innerHTML = warnings.map(w => `
        <div class="p-4 bg-orange-50 border border-orange-100 rounded-lg flex items-start text-sm text-orange-800">
            <i data-lucide="shield-alert" class="mr-3 mt-0.5 w-[18px] h-[18px] flex-shrink-0"></i><p>${w}</p>
        </div>`).join('');

    const secEl = document.getElementById('ai-risk-sections-list');
    if (secEl) secEl.innerHTML = sectionRisk.length > 0
        ? sectionRisk.map(s => `<div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg"><span class="font-bold text-gray-700 text-lg">${s.section}</span><span class="text-red-600 text-sm font-medium bg-red-50 px-3 py-1 rounded-full">${s.averageAbsences} avg absences</span></div>`).join('')
        : '<p class="text-sm text-gray-500">No high-risk sections detected.</p>';

    const stuEl = document.getElementById('ai-risk-students-body');
    if (stuEl) stuEl.innerHTML = riskList.slice(0, 5).map(s => `
        <tr class="border-b border-gray-50">
            <td class="px-6 py-3 font-medium">${s.student_id}</td>
            <td class="px-6 py-3">${s.name}</td>
            <td class="px-6 py-3">${s.section}</td>
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
            if (l.includes('fine') || l.includes('compute')) reply = "The AI computes escalating fines: 1st absence = ₱50, 2nd = ₱150, 3+ = ₱350.";
            else if (l.includes('student') || l.includes('add') || l.includes('qr')) reply = "Add students in the 'Student Masterlist' tab. The system generates a unique QR code for each!";
            else if (l.includes('event') || l.includes('alay')) reply = "Manage events in the 'Events Management' tab. Select the correct event before scanning!";
            else if (l.includes('hello') || l.includes('hi')) reply = "Hello Officer! How can I assist you with the attendance system?";
            else if (l.includes('ai') || l.includes('risk')) reply = "The AI Engine analyzes attendance patterns and identifies high-risk students and sections. Check the 'AI Analytics' tab!";
            window.chatMessages.push({ sender: 'bot', text: reply });
            window.renderChatMessages();
        }, 600);
    });
});
