document.addEventListener('DOMContentLoaded', function() {
    function updateTime() {
        const now = new Date();

        // Get date components
        const day = now.getDate();
        const month = now.toLocaleString('en-US', { month: 'long' });
        const year = now.getFullYear();
        
        // Get weekday name
        const weekday = now.toLocaleString('en-US', { weekday: 'long' });

        // Get time components in 12-hour format with AM/PM
        let hour = now.getHours();
        const minute = now.getMinutes().toString().padStart(2, '0');
        const ampm = hour >= 12 ? 'PM' : 'AM';
        
        // Convert 24-hour format to 12-hour format
        hour = hour % 12;
        hour = hour ? hour : 12; // If hour is 0, display 12 instead of 0
        
        // Update HTML elements
        document.getElementById('weekday').textContent = weekday;
        document.getElementById('day').textContent = day;
        document.getElementById('month').textContent = month;
        document.getElementById('year').textContent = year;
        document.getElementById('hour').textContent = hour;
        document.getElementById('minute').textContent = minute;
        document.getElementById('ampm').textContent = ampm; // Update the AM/PM part
    }

    // Update time every second
    setInterval(updateTime, 1000);

    // Initial call to display time immediately
    updateTime();
});
