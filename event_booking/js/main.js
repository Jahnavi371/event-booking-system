document.querySelectorAll(".book-btn").forEach(button => {
    button.addEventListener("click", function() {
        let eventId = this.getAttribute("data-event");
        fetch("book_event.php", {
            method: "POST",
            body: JSON.stringify({ event_id: eventId }),
            headers: { "Content-Type": "application/json" }
        }).then(response => response.text()).then(data => {
            alert(data);
            location.reload();
        });
    });
});
