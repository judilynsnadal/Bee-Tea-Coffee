document.addEventListener("DOMContentLoaded", () => {
    fetchProductsAndRender();
});

function fetchProductsAndRender() {
    const categories = ['milktea', 'fruittea', 'icedtea', 'frappe', 'coffee', 'others'];
    const grids = {};
    const sections = {};

    // Initialize references and clear grids
    categories.forEach(cat => {
        grids[cat] = document.getElementById(`grid-${cat}`);
        sections[cat] = document.getElementById(`section-${cat}`);
        if (grids[cat]) grids[cat].innerHTML = '';
        if (sections[cat]) sections[cat].style.display = 'none'; // hide until a product is added
    });

    fetch('fetch_products.php')
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                if (res.data.length === 0) {
                    const mainContainer = document.getElementById('dynamic-categories-container');
                    if (mainContainer) {
                        mainContainer.innerHTML = '<p style="text-align:center; width: 100%;">No products available.</p>';
                    }
                    return;
                }

                res.data.forEach(prod => {
                    const imgUrl = prod.image ? `../${prod.image}` : 'images/logo_btc.png';

                    // Categorization Logic based on product name
                    let category = "others";
                    const lname = prod.name.toLowerCase();

                    if (lname.includes("frappe")) {
                        category = "frappe";
                    } else if (lname.includes("tea")) {
                        if (lname.includes("fruit")) category = "fruittea";
                        else if (lname.includes("iced")) category = "icedtea";
                        else category = "milktea";
                    } else if (lname.includes("coffee") || lname.includes("mocha") || lname.includes("macchiato")) {
                        category = "coffee";
                    } else {
                        category = "others";
                    }

                    // Build product card (Customer side includes add-to-cart button)
                    const productHTML = `
                        <div class="product-card fade-up" data-category="${category}" data-price="${prod.price}" data-name="${lname}">
                            <img src="${imgUrl}" alt="${prod.name}" style="height: 200px; width: 100%; object-fit: cover;" />
                            <h3>${prod.name}</h3>
                            <p class="price">₱${parseFloat(prod.price).toFixed(2)}</p>
                            <button class="add-to-cart" data-id="${prod.id}" data-name="${prod.name}" data-price="${prod.price}" data-image="${imgUrl}">Add to Cart</button>
                        </div>
                    `;

                    // Inject into correct grid and reveal section
                    if (grids[category] && sections[category]) {
                        grids[category].insertAdjacentHTML('beforeend', productHTML);
                        sections[category].style.display = 'block';
                    }
                });

                // Re-initialize animations
                setTimeout(() => {
                    const fadeUpElements = document.querySelectorAll('.fade-up');
                    fadeUpElements.forEach(el => el.classList.add('show'));
                }, 100);
            }
        })
        .catch(err => console.error("Error fetching products:", err));
}
