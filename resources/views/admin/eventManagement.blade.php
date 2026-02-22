<!-- 2. EVENTS MANAGEMENT -->
<div id="tab-events" class="tab-content space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i data-lucide="calendar" class="mr-2 text-blue-600 w-5 h-5"></i> Create New Event
        </h3>
        <form id="add-event-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input required id="event-name" placeholder="Event Name (e.g. Alay sa Paaralan)" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none md:col-span-2" />
            <input required id="event-date" type="date" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none md:col-span-1" />
            <div class="md:col-span-1 flex space-x-2">
                <input required id="event-fine" type="number" min="20" max="50" placeholder="Fine ₱20-50" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none w-1/2" />
                <select id="event-type" class="border p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none w-1/2">
                    <option value="Mandatory">Mandatory</option>
                    <option value="Major">Major</option>
                    <option value="Voluntary">Voluntary</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-700 text-white font-bold rounded hover:bg-blue-800 transition-colors md:col-span-4 py-2">Add Event</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Event List</h3>
        </div>
        <table class="w-full text-left text-sm">
            <thead class="bg-white text-gray-500 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-medium">Event ID</th>
                    <th class="px-6 py-4 font-medium">Name</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium">Fine</th>
                    <th class="px-6 py-4 font-medium">Type</th>
                </tr>
            </thead>
            <tbody id="events-table-body">
                <!-- Rendered via JS -->
            </tbody>
        </table>
    </div>
</div>
