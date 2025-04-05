let shownFlights = 3;
const flights = document.querySelectorAll('.flight-card');
const showMoreBtn = document.getElementById('show-more-btn');

function showMoreFlights() {
    const remainingFlights = flights.length - shownFlights;
    const toShow = Math.min(5, remainingFlights);

    for (let i = shownFlights; i < shownFlights + toShow; i++) {
        if (flights[i]) {
            flights[i].classList.remove('hidden-flight');
        }
    }

    shownFlights += toShow;

    const remaining = flights.length - shownFlights;
    if (remaining > 0) {
        showMoreBtn.innerText = `Show more (${remaining})`;
    } else {
        showMoreBtn.style.display = 'none';
    }
}

if (flights.length <= 3) {
    showMoreBtn.style.display = 'none';
}

async function deleteFlight(flightId) {
    if (!confirm("Are you sure you want to delete this flight?")) return;

    const response = await fetch('/delete-flight', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ flightId })
    });

    if (response.ok) {
        const flightElement = document.querySelector(`.flight-card[data-id="${flightId}"]`);
        if (flightElement) {
            flightElement.remove(); 
            shownFlights--;
            const remaining = flights.length - shownFlights;
            if (remaining > 0) {
                showMoreBtn.innerText = `Show more (${remaining})`;
            } else {
                showMoreBtn.style.display = 'none';
            }
        }
    } else {
        alert('Failed to delete the flight');
    }
}
