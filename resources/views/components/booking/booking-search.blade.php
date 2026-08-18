<!-- Booking Search Modal -->
<div id="bookingModal" class="booking-overlay">

    <div class="booking-modal">

        <!-- Close Button -->
        <button type="button" class="close-btn" onclick="closeBookingModal()">
            &times;
        </button>

        <h2>Select Dates and Guests</h2>

        <form action="{{ url('/booking/search') }}" method="GET">

            <div class="booking-fields">

                <!-- Check-In -->
                <div class="booking-field">
                    <label for="check_in">Check-In date</label>

                    <div class="input-wrapper">
                        <span class="calendar-icon">▣</span>

                        <input
                            type="date"
                            id="check_in"
                            name="check_in"
                            value="{{ request('check_in', date('Y-m-d')) }}"
                            required
                        >
                    </div>
                </div>


                <!-- Check-Out -->
                <div class="booking-field">
                    <label for="check_out">Checkout date</label>

                    <div class="input-wrapper">
                        <span class="calendar-icon">▣</span>

                        <input
                            type="date"
                            id="check_out"
                            name="check_out"
                            value="{{ request('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                            required
                        >
                    </div>
                </div>


                <!-- Rooms & Guests -->
                <div class="booking-field">

                    <label>Rooms & Guests</label>

                    <div class="custom-dropdown">

                        <button
                            type="button"
                            class="dropdown-button"
                            onclick="toggleDropdown('guestDropdown')"
                        >
                            <span>
                                👤
                                <span id="guestSummary">
                                    1 Room, 1 Adult
                                </span>
                            </span>

                            <span>⌄</span>
                        </button>

                        <div
                            id="guestDropdown"
                            class="dropdown-content"
                        >

                            <!-- Rooms -->
                            <div class="counter-row">
                                <div>
                                    <strong>Rooms</strong>
                                </div>

                                <div class="counter">
                                    <button type="button" onclick="changeValue('rooms', -1)">
                                        −
                                    </button>

                                    <span id="roomsValue">1</span>

                                    <button type="button" onclick="changeValue('rooms', 1)">
                                        +
                                    </button>
                                </div>
                            </div>


                            <!-- Adults -->
                            <div class="counter-row">
                                <div>
                                    <strong>Adults</strong>
                                    <small>18+ years</small>
                                </div>

                                <div class="counter">

                                    <button type="button" onclick="changeValue('adults', -1)">
                                        −
                                    </button>

                                    <span id="adultsValue">1</span>

                                    <button type="button" onclick="changeValue('adults', 1)">
                                        +
                                    </button>

                                </div>
                            </div>


                            <!-- Children -->
                            <div class="counter-row">

                                <div>
                                    <strong>Children</strong>
                                    <small>0–17 years</small>
                                </div>

                                <div class="counter">

                                    <button type="button" onclick="changeValue('children', -1)">
                                        −
                                    </button>

                                    <span id="childrenValue">0</span>

                                    <button type="button" onclick="changeValue('children', 1)">
                                        +
                                    </button>

                                </div>

                            </div>

                            <button
                                type="button"
                                class="done-btn"
                                onclick="closeDropdown('guestDropdown')"
                            >
                                DONE
                            </button>

                        </div>

                    </div>

                    <!-- Hidden values submitted to Laravel -->
                    <input type="hidden" name="rooms" id="roomsInput" value="{{ request('rooms', 1) }}">
                    <input type="hidden" name="adults" id="adultsInput" value="{{ request('adults', 1) }}">
                    <input type="hidden" name="children" id="childrenInput" value="{{ request('children', 0) }}">

                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="accessible_room"
                            value="1"
                            {{ request()->boolean('accessible_room') ? 'checked' : '' }}
                        >
                        Accessible Room
                    </label>

                </div>


                <!-- Special Rates -->
                <div class="booking-field">

                    <label>Special Rates</label>

                    <div class="custom-dropdown">

                        <button
                            type="button"
                            class="dropdown-button"
                            onclick="toggleDropdown('rateDropdown')"
                        >

                            <span>
                                🏷️
                                <span id="rateSummary">
                                    Select Rate
                                </span>
                            </span>

                            <span>⌄</span>

                        </button>


                        <div
                            id="rateDropdown"
                            class="dropdown-content rate-dropdown"
                        >

                            <label class="rate-option">
                                <input
                                    type="radio"
                                    name="special_rate"
                                    value=""
                                    checked
                                    onchange="selectRate('Select Rate')"
                                >
                                Select Rate
                            </label>

                            <label class="rate-option">
                                <input
                                    type="radio"
                                    name="special_rate"
                                    value="aaa"
                                    onchange="selectRate('AAA / CAA')"
                                >
                                AAA / CAA
                            </label>

                            <label class="rate-option">
                                <input
                                    type="radio"
                                    name="special_rate"
                                    value="senior"
                                    onchange="selectRate('Senior Citizen')"
                                >
                                Senior Citizen
                            </label>

                            <label class="rate-option">
                                <input
                                    type="radio"
                                    name="special_rate"
                                    value="corporate"
                                    onchange="selectRate('Corporate')"
                                >
                                Corporate
                            </label>

                        </div>

                    </div>


                    <label class="checkbox-label">

                        <input
                            type="checkbox"
                            name="use_points"
                            value="1"
                            {{ request()->boolean('use_points') ? 'checked' : '' }}
                        >

                        Use Points

                    </label>

                </div>


                <!-- Book Button -->
                <div class="book-button-container">

                    <button
                        type="submit"
                        class="book-now-btn"
                    >
                        BOOK NOW
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<style>

    * {
        box-sizing: border-box;
    }

    /* Overlay */

    .booking-overlay {

        position: fixed;

        inset: 0;

        background: rgba(0, 0, 0, 0.70);

        backdrop-filter: blur(6px);

        display: none;

        align-items: center;

        justify-content: center;

        z-index: 99999;

        padding: 20px;
    }


    /* Modal */

    .booking-modal {

        position: relative;

        width: 100%;

        max-width: 1340px;

        background: #ffffff;

        padding: 52px 50px 55px;

        box-shadow: 0 10px 40px rgba(0,0,0,0.30);
    }


    /* Heading */

    .booking-modal h2 {

        margin: 0 0 40px;

        color: #970d16;

        font-family: Arial, sans-serif;

        font-size: 27px;

        font-weight: 700;
    }


    /* Close */

    .close-btn {

        position: absolute;

        right: 48px;

        top: 48px;

        width: 56px;

        height: 45px;

        background: white;

        border: 2px solid #970d16;

        color: #111;

        font-size: 40px;

        line-height: 35px;

        cursor: pointer;

        font-weight: 300;
    }

    .close-btn:hover {

        background: #970d16;

        color: white;
    }


    /* Main Fields */

    .booking-fields {

        display: grid;

        grid-template-columns:
            1.05fr
            1.05fr
            1.05fr
            1.05fr
            1.25fr;

        gap: 26px;

        align-items: start;
    }


    /* Labels */

    .booking-field > label:first-child {

        display: block;

        margin-bottom: 10px;

        font-family: Arial, sans-serif;

        font-size: 16px;

        font-weight: 700;

        color: #333;
    }


    /* Date input */

    .input-wrapper {

        position: relative;

        height: 48px;
    }

    .input-wrapper input {

        width: 100%;

        height: 48px;

        border: 1px solid #777;

        padding: 0 12px 0 50px;

        font-size: 17px;

        color: #444;

        background: white;
    }

    .calendar-icon {

        position: absolute;

        left: 14px;

        top: 12px;

        font-size: 22px;

        z-index: 2;

        color: #555;
    }


    /* Dropdown */

    .custom-dropdown {

        position: relative;

        width: 100%;
    }


    .dropdown-button {

        width: 100%;

        height: 48px;

        background: #fff;

        border: 1px solid #777;

        padding: 0 15px;

        display: flex;

        justify-content: space-between;

        align-items: center;

        font-size: 16px;

        color: #444;

        cursor: pointer;
    }


    .dropdown-content {

        display: none;

        position: absolute;

        left: 0;

        top: 54px;

        width: 100%;

        min-width: 280px;

        background: white;

        border: 1px solid #ccc;

        box-shadow: 0 5px 15px rgba(0,0,0,0.2);

        z-index: 100000;

        padding: 15px;
    }

    .dropdown-content.show {

        display: block;
    }


    /* Counter */

    .counter-row {

        display: flex;

        justify-content: space-between;

        align-items: center;

        padding: 12px 0;

        border-bottom: 1px solid #eee;
    }

    .counter-row strong {

        display: block;

        font-size: 15px;
    }

    .counter-row small {

        display: block;

        color: #777;

        margin-top: 3px;
    }


    .counter {

        display: flex;

        align-items: center;

        gap: 12px;
    }

    .counter button {

        width: 30px;

        height: 30px;

        border: 1px solid #777;

        background: white;

        cursor: pointer;

        font-size: 20px;
    }

    .counter span {

        min-width: 20px;

        text-align: center;

        font-weight: bold;
    }


    .done-btn {

        width: 100%;

        margin-top: 15px;

        height: 40px;

        background: #970d16;

        border: none;

        color: white;

        cursor: pointer;

        font-weight: bold;
    }


    /* Special Rate */

    .rate-option {

        display: block;

        padding: 12px;

        cursor: pointer;

        border-bottom: 1px solid #eee;

        font-size: 15px;
    }

    .rate-option:hover {

        background: #f5f5f5;
    }

    .rate-option input {

        margin-right: 8px;
    }


    /* Checkboxes */

    .checkbox-label {

        display: flex !important;

        align-items: center;

        gap: 8px;

        margin-top: 10px;

        font-size: 15px !important;

        font-weight: normal !important;

        color: #444 !important;

        cursor: pointer;
    }

    .checkbox-label input {

        width: 18px;

        height: 18px;
    }


    /* Book Now */

    .book-button-container {

        padding-top: 32px;
    }

    .book-now-btn {

        width: 100%;

        height: 48px;

        background: #970d16;

        color: white;

        border: none;

        font-size: 16px;

        font-weight: bold;

        cursor: pointer;

        letter-spacing: 0.2px;
    }

    .book-now-btn:hover {

        background: #720910;
    }


    /* Mobile */

    @media (max-width: 900px) {

        .booking-modal {

            max-height: 90vh;

            overflow-y: auto;

            padding: 35px 25px;
        }

        .booking-fields {

            grid-template-columns: 1fr;
        }

        .close-btn {

            right: 20px;

            top: 20px;

            width: 45px;

            height: 40px;

            font-size: 32px;
        }

        .booking-modal h2 {

            padding-right: 60px;

            font-size: 23px;
        }

        .book-button-container {

            padding-top: 10px;
        }
    }

</style>


<script>

    function formatDateInput(date) {
        const local = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
        return local.toISOString().slice(0, 10);
    }

    function initBookingState() {
        const rooms = Number(document.getElementById('roomsInput')?.value || 1);
        const adults = Number(document.getElementById('adultsInput')?.value || 1);
        const children = Number(document.getElementById('childrenInput')?.value || 0);

        document.getElementById('roomsValue').innerText = rooms;
        document.getElementById('adultsValue').innerText = adults;
        document.getElementById('childrenValue').innerText = children;

        const summary = document.getElementById('guestSummary');
        if (summary) {
            const roomText = rooms === 1 ? 'Room' : 'Rooms';
            const adultText = adults === 1 ? 'Adult' : 'Adults';
            const childText = children > 0 ? ', ' + children + ' Child' + (children > 1 ? 'ren' : '') : '';
            summary.innerText = rooms + ' ' + roomText + ', ' + adults + ' ' + adultText + childText;
        }

        const checkedRate = document.querySelector('input[name="special_rate"]:checked');
        const rateSummary = document.getElementById('rateSummary');
        if (checkedRate && rateSummary) {
            rateSummary.innerText = checkedRate.value ? checkedRate.closest('label')?.textContent?.trim() || 'Select Rate' : 'Select Rate';
        }

        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');
        if (checkIn && checkOut && checkIn.value) {
            const nextDay = new Date(checkIn.value + 'T12:00:00');
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.min = formatDateInput(nextDay);
            if (checkOut.value < checkOut.min) {
                checkOut.value = checkOut.min;
            }
        }
    }

    function openBookingModal() {
        const modal = document.getElementById('bookingModal');
        if (!modal) return;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeBookingModal() {
        const modal = document.getElementById('bookingModal');
        if (!modal) return;
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (!dropdown) return;

        document.querySelectorAll('.dropdown-content').forEach(function(item) {
            if (item.id !== id) {
                item.classList.remove('show');
            }
        });

        dropdown.classList.toggle('show');
    }

    function closeDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }

    let rooms = 1;
    let adults = 1;
    let children = 0;

    function changeValue(type, amount) {
        if (type === 'rooms') {
            rooms = Math.max(1, Number(document.getElementById('roomsInput')?.value || 1) + amount);
            document.getElementById('roomsValue').innerText = rooms;
            document.getElementById('roomsInput').value = rooms;
        }

        if (type === 'adults') {
            adults = Math.max(1, Number(document.getElementById('adultsInput')?.value || 1) + amount);
            document.getElementById('adultsValue').innerText = adults;
            document.getElementById('adultsInput').value = adults;
        }

        if (type === 'children') {
            children = Math.max(0, Number(document.getElementById('childrenInput')?.value || 0) + amount);
            document.getElementById('childrenValue').innerText = children;
            document.getElementById('childrenInput').value = children;
        }

        updateGuestSummary();
    }

    function updateGuestSummary() {
        const roomValue = Number(document.getElementById('roomsInput')?.value || 1);
        const adultValue = Number(document.getElementById('adultsInput')?.value || 1);
        const childValue = Number(document.getElementById('childrenInput')?.value || 0);

        const roomText = roomValue === 1 ? 'Room' : 'Rooms';
        const adultText = adultValue === 1 ? 'Adult' : 'Adults';
        const childText = childValue > 0 ? ', ' + childValue + ' Child' + (childValue > 1 ? 'ren' : '') : '';

        document.getElementById('guestSummary').innerText = roomValue + ' ' + roomText + ', ' + adultValue + ' ' + adultText + childText;
    }

    function selectRate(rate) {
        const summary = document.getElementById('rateSummary');
        if (summary) {
            summary.innerText = rate;
        }
        closeDropdown('rateDropdown');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.custom-dropdown')) {
            document.querySelectorAll('.dropdown-content').forEach(function(item) {
                item.classList.remove('show');
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        initBookingState();

        const checkIn = document.getElementById('check_in');
        const checkOut = document.getElementById('check_out');

        if (checkIn && checkOut) {
            checkIn.addEventListener('change', function() {
                const localCheckIn = new Date(this.value + 'T12:00:00');
                localCheckIn.setDate(localCheckIn.getDate() + 1);
                checkOut.min = formatDateInput(localCheckIn);

                if (!checkOut.value || checkOut.value <= this.value) {
                    checkOut.value = checkOut.min;
                }
            });
        }
    });

</script>