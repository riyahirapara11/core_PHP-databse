document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');

    // Fetch and populate countries
    fetch('/includes/countries.php?action=getCountries')
        .then(response => response.json())
        .then(countries => {
            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.id;
                option.textContent = country.name;
                countrySelect.appendChild(option);
            });
        });

    // Fetch states when country changes
    countrySelect.addEventListener('change', function () {
        const countryId = this.value;
        fetch(`/includes/countries.php?action=getStates&country_id=${countryId}`)
            .then(response => response.json())
            .then(states => {
                stateSelect.innerHTML = '<option value="">Select State</option>';
                states.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
            });
    });
});
