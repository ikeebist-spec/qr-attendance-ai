

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

    const yearAndSectionAbsences = {};
    const riskList = [];

    window.students.forEach(s => {
        let fine = s.fine || 0;

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


