function countryStateDropdowns(
  countrySelectId,
  stateSelectId,
  selectedCountry = "",
  selectedState = ""
) {
  const countrySelect = document.getElementById("country");
  const stateSelect = document.getElementById("state");

  fetch("http://localhost/core_PHP-databse/1/common/getCountryState.php?action=getCountries")
    .then((response) => response.json())
    .then((countries) => {
      countries.forEach((country) => {
        const option = document.createElement("option");
        option.value = country.id;
        option.textContent = country.name;

        if (country.id === selectedCountry) {
          option.selected = true;
        }
        countrySelect.appendChild(option);
      });
      if (selectedCountry) {
        fetchStates(selectedCountry, selectedState);
      }
    })
    .catch((error) => console.error("Error fetching countries:", error));

  countrySelect.addEventListener("change", function () {
    const countryId = this.value;
    stateSelect.innerHTML = '<option value="">Select State</option>';

    if (countryId) {
      fetchStates(countryId);
    }

    // Load states if a country is already selected (useful for pre-filled filters)
    if (countrySelect.value) {
      fetchStates(
        countrySelect.value,
        stateSelect.getAttribute("data-selected")
      );
    }
  });

//   function fetchStates(countryId, preselectedState = "") {
//     fetch(
//       `http://localhost/core_PHP-databse/1/common/getCountryState.php?action=getStates&country_id=${countryId}`
//     )
//       .then((response) => response.json())
//       .then((states) => {
//         console.log("Fetched states:", states);  // Debugging: Check the response

//         stateSelect.innerHTML = '<option value="">Select State</option>';

//         states.forEach((state) => {
//           const option = document.createElement("option");
//           option.value = state.id;
//           option.textContent = state.name;
//           if (state.id === preselectedState) {
//             option.selected = true;
//           }
//           stateSelect.appendChild(option);
//         });
//       })
//       .catch((error) => console.error("Error fetching states:", error));
//   }

document.addEventListener("DOMContentLoaded", function () {
    const countrySelect = document.getElementById("country");
    const stateSelect = document.getElementById("state");

    function fetchStates(countryId, preselectedState = "") {
        fetch(`../common/getCountryState.php?action=getStates&country_id=${countryId}`)
            .then(response => response.json())
            .then(states => {
                console.log("Fetched states:", states);  // Debugging: Check the response
                stateSelect.innerHTML = '<option value="">Select State</option>';
                states.forEach(state => {
                    const option = document.createElement("option");
                    option.value = state.id;
                    option.textContent = state.name;
                    if (state.id == preselectedState) {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching states:", error));
    }

    // Auto-load states if a country is already selected
    if (countrySelect.value) {
        fetchStates(countrySelect.value, stateSelect.getAttribute("data-selected"));
    }
});

}
