</body>
<script>
    function changeTimeSlots(selectElement) {
        Array.from(document.getElementsByClassName("timeSlots")).forEach(element => {
            element.style.display = 'none'
        })
        Array.from(document.getElementsByClassName("timeSlotRadio")).forEach(element => {
            element.checked = false
        })
        document.getElementById("software"+selectElement.value+"TimeSlots").style.display = 'block'
    }
    function clickLogout() {
        document.getElementById("employeeLogout").click()
    }
</script>
</html>