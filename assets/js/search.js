// Search Box Autocomplete with Debounce

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;

    // Create dynamic suggestion box and append to parent
    const suggestionBox = document.createElement('div');
    suggestionBox.className = 'autocomplete-suggestions';
    searchInput.parentElement.appendChild(suggestionBox);

    let debounceTimeout = null;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        clearTimeout(debounceTimeout);

        if (query.length < 2) {
            suggestionBox.innerHTML = '';
            suggestionBox.style.display = 'none';
            return;
        }

        // Debounce API calls by 300ms
        debounceTimeout = setTimeout(() => {
            fetch(`/smart-gov-portal/api/search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
                    
                    if (data.length === 0) {
                        suggestionBox.style.display = 'none';
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.innerHTML = `<strong>${item.name}</strong> <span style="font-size:11px; color:var(--text-muted);">(${item.category})</span>`;
                        
                        div.addEventListener('click', () => {
                            searchInput.value = item.name;
                            suggestionBox.style.display = 'none';
                            // Redirect directly to details page
                            window.location.href = `/smart-gov-portal/scheme-detail.php?id=${item.id}`;
                        });

                        suggestionBox.appendChild(div);
                    });

                    suggestionBox.style.display = 'block';
                })
                .catch(error => console.error('Error fetching search results:', error));
        }, 300);
    });

    // Close suggestion box if user clicks outside
    document.addEventListener('click', (e) => {
        if (e.target !== searchInput && e.target !== suggestionBox) {
            suggestionBox.style.display = 'none';
        }
    });
});
