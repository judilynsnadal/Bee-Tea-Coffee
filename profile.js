// ================= SIDEBAR PANEL TOGGLE =================
const profileLinks = document.querySelectorAll('.profile-menu a');
const contentPanels = document.querySelectorAll('.content-panel');

profileLinks.forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    const targetPanel = link.dataset.panel;

    // Save active panel to localStorage
    localStorage.setItem('activeProfilePanel', targetPanel);

    contentPanels.forEach(panel => {
      panel.style.display = panel.id === targetPanel ? 'block' : 'none';
    });

    profileLinks.forEach(l => l.parentElement.classList.remove('active'));
    link.parentElement.classList.add('active');
  });
});

// ================= PURCHASE TABS TOGGLE =================
const tabs = document.querySelectorAll('.orders-tabs .tab');
const tabPanels = document.querySelectorAll('.tab-panel');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    const target = tab.dataset.tab;
    // Save active order tab to localStorage
    localStorage.setItem('activeOrdersTab', target);

    tabPanels.forEach(panel => {
      panel.style.display = panel.id === target ? 'block' : 'none';
    });
  });
});

// ================= CART FUNCTIONALITY =================
// Cart is now handled server-side (profile.php)
// logic removed to prevent conflicts


// ================= HAMBURGER MENU TOGGLE =================
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('open');
});

// ================= DROPDOWN MENU =================
const userIcon = document.getElementById('userIcon');
const userDropdown = document.getElementById('userDropdown');

userIcon.addEventListener('click', e => {
  e.preventDefault();
  userDropdown.classList.toggle('show');
});

// Close dropdown if clicked outside
window.addEventListener('click', e => {
  if (!userIcon.contains(e.target) && !userDropdown.contains(e.target)) {
    userDropdown.classList.remove('show');
  }
});

// Profile photo updates are handled directly by the fileUpload listener in profile.php



// Removed broken sessionStorage line


// Update logic is now handled by update_profile.php form submission


// profile data!! 

// Profile data is now handled by PHP directly in profile.php
// The fetch logic has been removed to prevent conflicts.

// Logout logic removed - centralized in mainest.js










// ================= GCASH MODAL =================
function checkPaymentMethod() {
  const method = document.getElementById('paymentMethod').value;
  if (method === 'GCash') {
    // Automatically pop up modal when selected
    document.getElementById('gcash-modal-total').textContent = document.getElementById('cart-total').textContent;
    document.getElementById('gcashModal').style.display = "block";
  } else {
    closeGcashModal();
  }
}

function closeGcashModal() {
  document.getElementById('gcashModal').style.display = "none";
  // Reset payment method if they canceled
  if (document.getElementById('paymentMethod').value === 'GCash') {
    document.getElementById('paymentMethod').value = "";
  }
}

function confirmGcashPayment() {
  document.getElementById('gcashModal').style.display = "none";
  // They clicked I have paid, keep the select value as GCash
  // They still need to click Proceed to Checkout
  document.getElementById('paymentMethod').value = "GCash";
}

// ================= CHECKOUT =================
function proceedToCheckout() {
  const paymentMethod = document.getElementById('paymentMethod').value;
  const cartItems = document.querySelectorAll('.cart-item');

  if (cartItems.length === 0) {
    alert("Your cart is empty!");
    return;
  }

  if (!paymentMethod) {
    alert("Please select a payment method first.");
    return;
  }

  // If they click proceed without the modal showing, we could show it again:
  if (paymentMethod === 'GCash') {
    // Just confirming they actually paid via modal
    if (!confirm(`Proceed to checkout? Please ensure you have transferred the exact amount to GCash.`)) {
      return;
    }
  } else {
    if (!confirm(`Proceed to checkout with ${paymentMethod}?`)) {
      return;
    }
  }

  fetch('checkout.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ payment_method: paymentMethod })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Populate and show Receipt Modal
        const receiptModal = document.getElementById('receiptModal');
        const itemsContainer = document.getElementById('receipt-items');
        const refSpan = document.getElementById('receipt-ref');
        const totalSpan = document.getElementById('receipt-total-amount');
        const dateSpan = document.getElementById('receipt-date');
        const paymentSpan = document.getElementById('receipt-payment');
        const customerNameSpan = document.getElementById('receipt-customer-name');
        const customerPhoneSpan = document.getElementById('receipt-customer-phone');
        const customerAddressSpan = document.getElementById('receipt-customer-address');

        // Set basic info
        refSpan.textContent = data.transaction_ref;
        totalSpan.textContent = '₱' + data.total_price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        dateSpan.textContent = new Date().toLocaleString();
        paymentSpan.textContent = data.payment_method;
        customerNameSpan.textContent = data.customer_name;
        customerPhoneSpan.textContent = data.customer_phone;
        customerAddressSpan.textContent = data.customer_address;

        // Populate items
        itemsContainer.innerHTML = '';
        data.order_items.forEach(item => {
          const itemRow = document.createElement('div');
          itemRow.className = 'receipt-row';
          itemRow.innerHTML = `
            <span>${item.product_name} (x${item.quantity})</span>
            <span>₱${item.item_total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
          `;
          itemsContainer.appendChild(itemRow);

          // Show size and addons if any
          let detailsText = `Size: ${item.size}`;
          if (item.addons && item.addons.trim() !== '') {
            const formattedAddons = item.addons.split(',')
              .map(a => a.trim().replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()))
              .join(', ');
            detailsText += ` | Add-ons: ${formattedAddons}`;
          }
          const detailsRow = document.createElement('div');
          detailsRow.className = 'receipt-item-details';
          detailsRow.textContent = detailsText;
          itemsContainer.appendChild(detailsRow);
        });

        // Show the modal
        receiptModal.style.display = 'block';

        // Set session storage to open purchase tab after reload (which happens when closing modal)
        sessionStorage.setItem('openPurchaseTab', 'true');
      } else {
        alert('Checkout failed: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error during checkout:', error);
      alert('An error occurred during checkout.');
    });
}

function closeReceiptModal() {
  document.getElementById('receiptModal').style.display = 'none';
  location.reload();
}

// ================= UPDATE CART QUANTITY =================
// ================= UPDATE CART QUANTITY =================
function updateQuantity(cartId, change) {
  fetch('update_cart_quantity.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ cart_id: cartId, change: change })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Find input box instead of span
        const qtyInput = document.getElementById('qty-' + cartId);
        if (qtyInput) {
          qtyInput.value = data.new_quantity;
        }
        updateCartTotal(); // Recalculate total
      } else {
        alert('Error updating quantity: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating quantity.');
    });
}

function updateQuantityDirect(cartId, newValue) {
  fetch('update_cart_quantity.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ cart_id: cartId, new_value: newValue, absolute: true })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateCartTotal(); // Recalculate total
      } else {
        alert('Error updating quantity: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating quantity.');
    });
}

// ================= UPDATE CART SIZE =================
function updateSize(cartId, size, btnElement) {
  fetch('update_size.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ cart_id: cartId, size: size })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Find the parent size-controls div
        const controls = btnElement.closest('.size-controls');
        // Remove active class and red background from all buttons in this row
        const btns = controls.querySelectorAll('.size-btn');
        btns.forEach(b => {
          b.classList.remove('active-size');
          b.style.background = 'transparent';
          b.style.color = '#333';
        });

        // Add active styling to clicked button
        btnElement.classList.add('active-size');
        btnElement.style.background = '#f20608';
        btnElement.style.color = '#fff';

        // Update item price internally
        const cartItem = btnElement.closest('.cart-item');
        if (cartItem) {
          const basePrice = parseFloat(cartItem.dataset.basePrice);
          const addonBtns = cartItem.querySelectorAll('.addon-btn.active-addon');
          const addonsCount = addonBtns ? addonBtns.length : 0;
          const newPrice = basePrice + (size === '20oz' ? 10 : 0) + (addonsCount * 10);
          cartItem.dataset.price = newPrice;
          updateCartTotal();
        }
      } else {
        alert('Error updating size: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating size.');
    });
}

// ================= UPDATE CART ADD-ON =================
function toggleAddon(cartId, addonType, btnElement) {
  // Determine if we are adding or removing the addon based on the current active state
  const isCurrentlyActive = btnElement.classList.contains('active-addon');
  const action = isCurrentlyActive ? 'remove' : 'add';

  fetch('update_addon.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    credentials: 'same-origin',
    body: JSON.stringify({ cart_id: cartId, addon: addonType, action: action })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        if (action === 'add') {
          btnElement.classList.add('active-addon');
          btnElement.style.background = '#f20608';
          btnElement.style.color = '#fff';
        } else {
          btnElement.classList.remove('active-addon');
          btnElement.style.background = 'transparent';
          btnElement.style.color = '#333';
        }

        // Update item price internally
        const cartItem = btnElement.closest('.cart-item');
        if (cartItem) {
          const basePrice = parseFloat(cartItem.dataset.basePrice);
          const size = cartItem.querySelector('.size-btn.active-size');
          const is20oz = size ? size.textContent === '20oz' : false;

          const addonBtns = cartItem.querySelectorAll('.addon-btn.active-addon');
          const addonsCount = addonBtns ? addonBtns.length : 0;

          const newPrice = basePrice + (is20oz ? 10 : 0) + (addonsCount * 10);
          cartItem.dataset.price = newPrice;
          updateCartTotal();
        }
      } else {
        alert('Error updating addon: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred while updating addon.');
    });
}

function updateCartTotal() {
  let total = 0;
  const cartItems = document.querySelectorAll('.cart-item');
  cartItems.forEach(item => {
    const price = parseFloat(item.dataset.price);
    const qtyInput = item.querySelector('input[id^="qty-"]');
    if (qtyInput) {
      const qty = parseInt(qtyInput.value);
      if (!isNaN(price) && !isNaN(qty)) {
        const itemTotal = price * qty;
        total += itemTotal;

        const priceDisplay = item.querySelector('.price');
        if (priceDisplay) {
          priceDisplay.textContent = '₱' + itemTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
      }
    }
  });

  const totalEl = document.getElementById('cart-total');
  if (totalEl) {
    totalEl.textContent = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
}

// ================= ORDER SEARCH =================
document.addEventListener('DOMContentLoaded', () => {
  const orderSearchInput = document.getElementById('orderSearchInput');
  if (orderSearchInput) {
    orderSearchInput.addEventListener('input', function () {
      const query = this.value.toLowerCase().trim();
      const orderCards = document.querySelectorAll('.order-card');

      orderCards.forEach(card => {
        const orderId = card.querySelector('.order-ref').textContent.toLowerCase();
        const productName = card.querySelector('h4').textContent.toLowerCase();

        if (orderId.includes(query) || productName.includes(query)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });

      // Handle empty search results within active tab
      document.querySelectorAll('.tab-panel').forEach(panel => {
        if (panel.style.display === 'block') {
          const visibleCards = panel.querySelectorAll('.order-card[style="display: block;"]');
          const emptyMsg = panel.querySelector('.empty-search-state');

          if (visibleCards.length === 0 && query !== '') {
            if (!emptyMsg) {
              const msg = document.createElement('div');
              msg.className = 'empty-search-state';
              msg.style.textAlign = 'center';
              msg.style.padding = '20px';
              msg.innerHTML = `<p>No orders matching "${query}"</p>`;
              panel.querySelector('.order-list').appendChild(msg);
            } else {
              emptyMsg.innerHTML = `<p>No orders matching "${query}"</p>`;
              emptyMsg.style.display = 'block';
            }
          } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
          }
        }
      });
    });
  }
});

// Calculate total on load
document.addEventListener('DOMContentLoaded', () => {
  updateCartTotal();

  // 1. Restore Active Sidebar Panel
  const urlParams = new URLSearchParams(window.location.search);
  const paneParam = urlParams.get('pane');
  const savedPanel = localStorage.getItem('activeProfilePanel');
  const activePanel = paneParam || savedPanel;

  if (activePanel) {
    const link = document.querySelector(`.profile-menu a[data-panel="${activePanel}"]`);
    if (link) {
      // Logic for panel switching is already in profile.php (PHP-side), 
      // but clicking the link ensures the sidebar 'active' class is correctly managed if JS is used later.
      // However, link.click() would trigger e.preventDefault() and might interfere with the PHP-driven initial state.
      // Since PHP already set the panel, we just need to ensure the sidebar highlight is correct.
      // profileLinks.forEach(l => l.parentElement.classList.remove('active'));
      // link.parentElement.classList.add('active');
    }
  } else if (sessionStorage.getItem('openPurchaseTab') === 'true') {
    sessionStorage.removeItem('openPurchaseTab');
    const purchaseLink = document.querySelector('.profile-menu a[data-panel="my-purchase"]');
    if (purchaseLink) {
      purchaseLink.click();
    }
  }

  // 2. Restore Active Purchase Tab
  const savedOrdersTab = localStorage.getItem('activeOrdersTab');
  if (savedOrdersTab) {
    const orderTabLink = document.querySelector(`.orders-tabs .tab[data-tab="${savedOrdersTab}"]`);
    if (orderTabLink) {
      orderTabLink.click();
    }
  }
});

// ================= AUTO-REFRESH ORDERS & CART =================
let lastOrdersHTML = "";

setInterval(() => {
  fetch('profile.php?t=' + new Date().getTime())
    .then(response => response.text())
    .then(html => {
      // Create a temporary DOM parser to read the incoming HTML without reloading the page
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // 1. Update the My Purchase Tabs (Orders)
      const currentOrdersContainer = document.querySelector('.tab-content');
      const newOrdersContainer = doc.querySelector('.tab-content');

      if (currentOrdersContainer && newOrdersContainer) {
        // Only update if the content has physically changed to prevent screen flashing
        if (newOrdersContainer.innerHTML !== lastOrdersHTML) {
          currentOrdersContainer.innerHTML = newOrdersContainer.innerHTML;
          lastOrdersHTML = newOrdersContainer.innerHTML;
        }
      }

      // 2. Update the Cart Total
      // We do not overwrite the current cart total with the server's raw HTML response here
      // because the JS calculates the correct total locally and updates it, 
      // preventing a flash to 0.00 from the uncalculated server PHP default.

    })
    .catch(err => console.error("Auto-refresh error:", err));
}, 5000); // Check for new updates every 5 seconds
