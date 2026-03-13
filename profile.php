<?php
session_start();
include("connect.php");
include("encryption_utils.php");

// Handle panel navigation from URL
$active_pane = isset($_GET['pane']) ? $_GET['pane'] : 'my-account';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM userrs WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
  // Decrypt user data
  $user['fullname'] = decryptData($user['fullname']);
  $user['email'] = decryptData($user['email']);
  $user['contact_number'] = decryptData($user['contact_number']);
  $user['address'] = decryptData($user['address']);
  $user['username'] = decryptData($user['username']);
  $user['birthdate'] = decryptData($user['birthdate']);
  $user['photo'] = decryptData($user['photo']);
}
else {
  echo "<script>alert('User not found!'); window.location='login.html';</script>";
  exit();
}

// Fetch Cart Items
$cart_sql = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$cart_result = $conn->query($cart_sql);

// Fetch Orders
$order_sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$order_result = $conn->query($order_sql);
$orders = [];
if ($order_result) {
  while ($row = $order_result->fetch_assoc()) {
    $orders[] = $row;
  }
}
// Helper to filter orders by status
function getOrdersByStatus($orders, $status)
{
  if ($status === 'all')
    return $orders;
  return array_filter($orders, function ($o) use ($status) {
    return strtolower(trim($o['status'])) === strtolower(trim($status));
  });
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bea Tea Coffee | My Profile</title>
  <link rel="icon" href="images/logo_btc.png" type="image/png" />

  <!-- CSS -->
  <link rel="stylesheet" href="profile.css?v=<?php echo time(); ?>">
  <!-- FONT AWESOME -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <!-- NAVIGATION -->
  <nav>
    <div class="nav-container">
      <a href="homecus.html" class="logo">
        <img src="images/logo_btc.png" alt="Bea Tea Coffee Logo" />
        <span>Bea Tea Coffee</span>
      </a>

      <ul class="nav-links" id="navLinks">
        <li><a href="homecus.html">Home</a></li>
        <li><a href="products.html">Products</a></li>
        <li><a href="gallery.html">Gallery</a></li>
        <li><a href="about.html">About</a></li>

        <li><a href="profile.php?pane=my-purchase" title="My Purchase"><i class="fa-solid fa-bag-shopping"></i></a></li>
        <li><a href="profile.php?pane=my-cart" title="My Cart"><i class="fa-solid fa-cart-shopping"></i></a></li>

        <!-- DROPDOWN BUTTON -->
        <li class="user-menu">
          <a href="#" id="userIcon" class="user-tab">
            <i class="fa-solid fa-user"></i>
          </a>
          <ul class="dropdown-menu" id="userDropdown">
            <li><a href="profile.php?pane=my-account">My Profile</a></li>
            <li><a href="logout.php" id="logoutBtn">Logout</a></li>
          </ul>
        </li>
      </ul>

      <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </nav>

  <!-- PROFILE SECTION -->
  <section class="profile-section">
    <div class="profile-container">
      <!-- SIDEBAR -->
      <aside class="profile-sidebar">
        <div class="profile-user">
          <!-- Display user avatar if set and file exists, else fallback to logo -->
          <?php 
            $sidebar_photo = !empty($user['photo']) ? 'uploads/' . $user['photo'] : '';
            if (empty($sidebar_photo) || !file_exists($sidebar_photo)) {
                $sidebar_photo = 'images/logo_btc.png';
            }
          ?>
          <img src="<?php echo $sidebar_photo; ?>" alt="User Avatar" id="sidebarAvatar" />
          <h3><?php echo htmlspecialchars(!empty($user['fullname']) ? $user['fullname'] : $user['email']); ?></h3>
        </div>

        <ul class="profile-menu">
          <li class="<?php echo($active_pane === 'my-account') ? 'active' : ''; ?>">
            <a href="profile.php?pane=my-account" data-panel="my-account">
              <i class="fa-solid fa-user"></i> My Account
            </a>
          </li>
          <li class="<?php echo($active_pane === 'my-purchase') ? 'active' : ''; ?>">
            <a href="profile.php?pane=my-purchase" data-panel="my-purchase">
              <i class="fa-solid fa-bag-shopping"></i> My Purchase
            </a>
          </li>
          <li class="<?php echo($active_pane === 'my-cart') ? 'active' : ''; ?>">
            <a href="profile.php?pane=my-cart" data-panel="my-cart">
              <i class="fa-solid fa-cart-shopping"></i> My Cart
            </a>
          </li>
        </ul>
      </aside>

      <!-- MAIN CONTENT -->
      <div class="profile-main">

        <!-- MY ACCOUNT -->
        <div class="content-panel" id="my-account" style="<?php echo($active_pane === 'my-account') ? 'display: block;' : 'display: none;'; ?>">
          <div class="profile-wrapper">
            <h2>My Profile</h2>
            <p class="subtitle">Manage and protect your account</p>
            <hr />

            <form class="profile-form" action="update_profile.php" method="POST" enctype="multipart/form-data">
              <div class="form-grid">
                <!-- LEFT SIDE -->
                <div class="form-left">
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" placeholder="Enter your username" readonly />
                    <small>Username cannot be changed.</small>
                  </div>

                  <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" placeholder="Enter your name" />
                  </div>

                  <div class="form-group">
                    <label>Email</label>
                    <div class="inline">
                      <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Enter your email" />
                  </div>
                  </div>

                  <div class="form-group">
                    <label>Address</label>
                    <div class="inline">
                      <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" placeholder="Enter your address" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Phone Number</label>
                    <div class="inline">
                      <?php 
                        // Format the stored number for display if it's 11 digits
                        $display_contact = htmlspecialchars($user['contact_number']);
                        if (strlen($display_contact) === 11) {
                          $display_contact = substr($display_contact, 0, 4) . '-' . substr($display_contact, 4, 3) . '-' . substr($display_contact, 7);
                        }
                      ?>
                      <input type="text" name="contact_number" value="<?php echo $display_contact; ?>" 
                        placeholder="09XX-XXX-YYYY" maxlength="13" 
                        oninput="formatPhoneNumber(this)"
                        title="Format: 09XX-XXX-YYYY" required />
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-options">
                      <label><input type="radio" name="gender" value="Male" <?php if ($user['gender'] == 'Male')
  echo 'checked'; ?> /> Male</label>
                      <label><input type="radio" name="gender" value="Female" <?php if ($user['gender'] == 'Female')
  echo 'checked'; ?> /> Female</label>
                      <label><input type="radio" name="gender" value="Other" <?php if ($user['gender'] == 'Other')
  echo 'checked'; ?> /> Other</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Date of Birth</label>
                    <div class="inline">
                      <!-- Assuming column name is date_of_birth or similar based on signup form, but wait, signup.php didn't insert birthdate! Update profile form had birthdate HTML but logic... wait. -->
                      <!-- Let's check update_profile.php again. It doesn't update birthdate. -->
                      <input type="date" name="birthdate" value="<?php echo isset($user['birthdate']) ? htmlspecialchars($user['birthdate']) : ''; ?>" placeholder="MM/DD/YYYY" />
                    </div>
                  </div>

                  <button type="submit" class="save-btn">Save</button>
                </div>

                <!-- RIGHT SIDE -->
                <div class="form-right">
                  <div class="profile-photo">
                     <?php 
                       $main_photo = !empty($user['photo']) ? 'uploads/' . $user['photo'] : '';
                       if (empty($main_photo) || !file_exists($main_photo)) {
                           $main_photo = 'images/logo_btc.png';
                       }
                     ?>
                     <img src="<?php echo $main_photo; ?>" alt="Profile Photo" id="profileImage" />
                    <label for="fileUpload" style="cursor: pointer; color: #f20608; display: block; margin-top: 5px;">Select Image</label>
                    <input type="file" name="photo" id="fileUpload" style="display: none;" accept="image/jpeg, image/png">
                    <p class="note">
                      File size: maximum 1 MB <br />
                      File extension: .JPEG, .PNG
                    </p>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- MY PURCHASE -->
        <div class="content-panel" id="my-purchase" style="<?php echo($active_pane === 'my-purchase') ? 'display: block;' : 'display: none;'; ?>">
          <h2 class="section-title">My Purchase</h2>

          <!-- Orders Tabs -->
          <div class="orders-tabs">
            <button class="tab active" data-tab="all">All</button>
            <button class="tab" data-tab="to-pay">To Pay</button>
            <button class="tab" data-tab="to-ship">To Ship</button>
            <button class="tab" data-tab="to-receive">To Receive</button>
            <button class="tab" data-tab="completed">Completed</button>
            <button class="tab" data-tab="cancelled">Cancelled</button>
            <button class="tab" data-tab="refund">Refund</button>
          </div>

          <!-- Search Bar -->
          <div class="orders-search">
            <i class="fa fa-search"></i>
            <input type="text" id="orderSearchInput" placeholder="Search by Seller Name, Order ID or Product name" />
          </div>

          <!-- Orders Tab Panels -->
          <div class="tab-content">
            <?php
$tabs = [
  'all' => 'All',
  'to-pay' => 'Pending',
  'to-ship' => 'In Transit',
  'to-receive' => 'Shipped',
  'completed' => 'Delivered',
  'cancelled' => 'Cancelled',
  'refund' => 'Refund'
];

foreach ($tabs as $key => $status_label):
  $filtered_orders = getOrdersByStatus($orders, ($key === 'all') ? 'all' : $status_label);


?>
            <div class="tab-panel" id="<?php echo $key; ?>" style="<?php echo($key === 'all') ? 'display: block;' : 'display: none;'; ?>">
              <?php if (empty($filtered_orders)): ?>
                  <div class="empty-state">
                    <img src="images/empty_orders.png" alt="No orders" />
                    <p>No orders <?php echo($key !== 'all') ? 'in ' . $status_label : 'yet'; ?></p>
                  </div>
              <?php
  else: ?>
                  <div class="order-list">
                    <?php foreach ($filtered_orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <span class="order-ref">Order ID: <?php echo htmlspecialchars($order['transaction_ref']); ?></span>
                                <span class="order-status status-<?php echo strtolower(str_replace(' ', '-', $order['status'])); ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                            </div>
                            <div class="order-body">
                                <!-- Actual product image -->
                                <?php $order_imgSrc = !empty($order['image']) ? htmlspecialchars($order['image']) : 'images/logo_btc.png'; ?>
                                <img src="<?php echo $order_imgSrc; ?>" alt="Product" class="order-product-img"> 
                                <div class="order-info">
                                    <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                    <p class="order-qty" style="margin-bottom: 2px;">Qty: <?php echo $order['quantity']; ?></p>
                                    <?php if (!empty($order['size'])): ?>
                                        <p class="order-qty" style="margin-bottom: 2px;">Size: <?php echo htmlspecialchars($order['size']); ?></p>
                                    <?php
      endif; ?>
                                    
                                    <?php
      $o_addons = isset($order['addons']) ? $order['addons'] : '';
      if (!empty($o_addons)):
        $addons_arr = array_filter(array_map('trim', explode(',', $o_addons)));
        if (count($addons_arr) > 0):
          // Format addons for display (e.g. from 'cream_cheese' to 'Cream Cheese')
          $formatted_addons = array_map(function ($a) {
            return ucwords(str_replace('_', ' ', $a));
          }, $addons_arr);
          $addons_str = implode(', + ', $formatted_addons);
?>
                                        <p class="order-qty" style="margin-bottom: 2px; color: #f20608; font-size: 0.85rem;">[+ <?php echo $addons_str; ?>]</p>
                                    <?php
        endif;
      endif;
?>
                                    
                                    <p class="order-qty" style="margin-bottom: 5px;">Payment: <strong><?php echo htmlspecialchars($order['payment_method']); ?></strong></p>
                                    <p class="order-price">₱<?php echo number_format($order['price'], 2); ?></p>
                                </div>
                            </div>
                            <div class="order-footer">
                                <span class="order-total-label">Total:</span> 
                                <span class="order-total-amount">₱<?php echo number_format($order['price'] * $order['quantity'], 2); ?></span>
                            </div>
                        </div>
                    <?php
    endforeach; ?>
                  </div>
              <?php
  endif; ?>
            </div>
            <?php
endforeach; ?>
          </div>
        </div>

        <script>
          function showPanel(panelId) {
            document.querySelectorAll('.content-panel').forEach(panel => {
              panel.style.display = 'none';
            });
            document.getElementById(panelId).style.display = 'block';
          }

          // Simple JS for tab switching inside My Purchase
           document.querySelectorAll('.orders-tabs .tab').forEach(tab => {
            tab.addEventListener('click', () => {
              document.querySelectorAll('.orders-tabs .tab').forEach(t => t.classList.remove('active'));
              tab.classList.add('active');
              
              const target = tab.dataset.tab;
              document.querySelectorAll('.tab-panel').forEach(panel => {
                  /* Logic for "all" is tricky if panels are separate divs. 
                     The original code had structure:
                     <div class="tab-panel" id="all">...</div>
                     <div class="tab-panel" id="to-pay">...</div>
                     So showing "all" meant showing the #all div.
                  */
                 panel.style.display = (panel.id === target) ? 'block' : 'none';
              });
            });
          });

          // Handle file input preview
          // Handle file input preview for both main profile and sidebar
          document.getElementById('fileUpload').addEventListener('change', function(e) {
              if (e.target.files && e.target.files[0]) {
                  var reader = new FileReader();
                  reader.onload = function(e) {
                      document.getElementById('profileImage').src = e.target.result;
                      const sidebarAvatar = document.getElementById('sidebarAvatar');
                      if (sidebarAvatar) {
                          sidebarAvatar.src = e.target.result;
                      }
                  }
                  reader.readAsDataURL(e.target.files[0]);
              }
          });
        </script>

        <!-- MY CART -->
        <div class="content-panel" id="my-cart" style="<?php echo($active_pane === 'my-cart') ? 'display: block;' : 'display: none;'; ?>">
          <h2 class="section-title">My Cart</h2>



          <!-- Cart Items -->
          <div class="cart-items">
            <?php
$cart_total_price = 0;
if ($cart_result->num_rows > 0): ?>
              <?php while ($item = $cart_result->fetch_assoc()): ?>
                <?php
    // Handle potential column name differences
    $p_image = !empty($item['product_image']) ? $item['product_image'] : (isset($item['image']) ? $item['image'] : '');
    $p_name = !empty($item['product_name']) ? $item['product_name'] : (isset($item['product']) ? $item['product'] : 'Unknown Product');
    $base_price = !empty($item['product_price']) ? $item['product_price'] : (isset($item['price']) ? $item['price'] : 0);
    $p_size = isset($item['size']) ? $item['size'] : '16oz';
    $p_price = ($p_size === '20oz') ? $base_price + 10 : $base_price;
    $p_quantity = $item['quantity'];

    // Check if addons was added in the DB to add to price
    $p_addon = isset($item['addons']) ? $item['addons'] : '';
    // Let's assume the DB could store comma-separated addons "pearl,nata" or single string
    $active_addons = array_filter(array_map('trim', explode(',', $p_addon)));
    $addon_count = count($active_addons);

    $p_price += ($addon_count * 10); // Assume every addon is 10

    $cart_total_price += ($p_price * $p_quantity);

    $imgSrc = htmlspecialchars($p_image);
?>
                <div class="cart-item" data-id="<?php echo $item['id']; ?>" data-base-price="<?php echo $base_price; ?>" data-price="<?php echo $p_price; ?>">
                  <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($p_name); ?>">
                   <div class="cart-item-details">
                     <h4><?php echo htmlspecialchars($p_name); ?></h4>
                     <p class="price">₱<?php echo number_format($p_price, 2); ?></p>
                     <div class="quantity-controls" style="display: flex; align-items: center; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px; padding: 2px; width: fit-content;">
                        <div style="display: flex; align-items: center;">
                            <input type="number" id="qty-<?php echo $item['id']; ?>" value="<?php echo $p_quantity; ?>" min="1" max="99" 
                                   onchange="updateQuantityDirect(<?php echo $item['id']; ?>, this.value)" 
                                   style="width: 50px; padding: 5px; border: none; border-right: 1px solid #ccc; text-align: center; font-size: 1rem; outline: none; background: transparent;">
                        </div>
                        <?php
    $p_size = isset($item['size']) ? $item['size'] : '16oz';
    $p_addon = isset($item['addons']) ? $item['addons'] : '';
    // DB can hold a comma-separated string `pearl,nata` 
    $addons_arr = array_filter(array_map('trim', explode(',', $p_addon)));
    $has_pearl = in_array('pearl', $addons_arr);
    $has_nata = in_array('nata', $addons_arr);
    $has_cream_cheese = in_array('cream_cheese', $addons_arr);
    $has_coffee_jelly = in_array('coffee_jelly', $addons_arr);
?>
                        <div class="size-controls" style="display: flex; gap: 5px; margin-left: 10px;">
                            <button class="size-btn <?php echo($p_size == '16oz') ? 'active-size' : ''; ?>" 
                                    onclick="updateSize(<?php echo $item['id']; ?>, '16oz', this)" 
                                    style="padding: 5px 12px; border: 1px solid #ccc; background: <?php echo($p_size == '16oz') ? '#f20608' : 'transparent'; ?>; color: <?php echo($p_size == '16oz') ? '#fff' : '#333'; ?>; cursor: pointer; border-radius: 4px; font-size: 0.95rem; font-weight: bold;">16oz</button>
                            <button class="size-btn <?php echo($p_size == '20oz') ? 'active-size' : ''; ?>" 
                                    onclick="updateSize(<?php echo $item['id']; ?>, '20oz', this)" 
                                    style="padding: 5px 12px; border: 1px solid #ccc; background: <?php echo($p_size == '20oz') ? '#f20608' : 'transparent'; ?>; color: <?php echo($p_size == '20oz') ? '#fff' : '#333'; ?>; cursor: pointer; border-radius: 4px; font-size: 0.95rem; font-weight: bold;">20oz</button>
                        </div>
                      </div>
                      
                      <!-- Add Ons Section below Quantity and Size -->
                      <div class="addons-section" style="margin-top: 10px;">
                          <p style="font-size: 0.85rem; color: #666; margin-bottom: 5px; font-weight: bold;">Add Ons:</p>
                          <div class="addons-options" style="display: flex; gap: 8px; flex-wrap: wrap;">
                              <button class="addon-btn <?php echo($has_pearl) ? 'active-addon' : ''; ?>" 
                                      onclick="toggleAddon(<?php echo $item['id']; ?>, 'pearl', this)" 
                                      style="padding: 4px 10px; border: 1px solid #ccc; background: <?php echo($has_pearl) ? '#f20608' : 'transparent'; ?>; color: <?php echo($has_pearl) ? '#fff' : '#333'; ?>; cursor: pointer; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">+ Pearl</button>
                                      
                              <button class="addon-btn <?php echo($has_nata ? 'active-addon' : ''); ?>" 
                                      onclick="toggleAddon(<?php echo $item['id']; ?>, 'nata', this)" 
                                      style="padding: 4px 10px; border: 1px solid #ccc; background: <?php echo($has_nata ? '#f20608' : 'transparent'); ?>; color: <?php echo($has_nata ? '#fff' : '#333'); ?>; cursor: pointer; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">+ Nata</button>
                                      
                              <button class="addon-btn <?php echo($has_cream_cheese ? 'active-addon' : ''); ?>" 
                                      onclick="toggleAddon(<?php echo $item['id']; ?>, 'cream_cheese', this)" 
                                      style="padding: 4px 10px; border: 1px solid #ccc; background: <?php echo($has_cream_cheese ? '#f20608' : 'transparent'); ?>; color: <?php echo($has_cream_cheese ? '#fff' : '#333'); ?>; cursor: pointer; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">+ Cream Cheese</button>
                                      
                              <button class="addon-btn <?php echo($has_coffee_jelly ? 'active-addon' : ''); ?>" 
                                      onclick="toggleAddon(<?php echo $item['id']; ?>, 'coffee_jelly', this)" 
                                      style="padding: 4px 10px; border: 1px solid #ccc; background: <?php echo($has_coffee_jelly ? '#f20608' : 'transparent'); ?>; color: <?php echo($has_coffee_jelly ? '#fff' : '#333'); ?>; cursor: pointer; border-radius: 4px; font-size: 0.85rem; font-weight: bold;">+ Coffee Jelly</button>
                          </div>
                      </div>

                   </div>
                  <div class="cart-item-actions">
                    <i class="fa fa-trash remove-icon" onclick="removeCartItem(<?php echo $item['id']; ?>)"></i>
                  </div>
                </div>
              <?php
  endwhile; ?>
            <?php
else: ?>
              <div class="empty-state" id="emptyCart">
                <i class="fa fa-shopping-cart fa-3x"></i>
                <p>Your cart is empty</p>
              </div>
            <?php
endif; ?>
          </div>

        <!-- Cart Footer with Total -->
        <div class="cart-footer" style="padding: 20px; border-top: 1px solid #eee; margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h3>Total: ₱<span id="cart-total"><?php echo number_format($cart_total_price ?? 0, 2); ?></span></h3>
            <div class="checkout-container" style="margin-top: 0; display: flex; gap: 10px;">
                <select id="paymentMethod" class="payment-select" onchange="checkPaymentMethod()">
                    <option value="" disabled selected>Select Payment Method</option>
                    <option value="COD">Cash on Delivery (COD)</option>
                    <option value="GCash">GCash</option>
                    <option value="Walk-in">Pick-up / Walk-in</option>
                </select>
                <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>
  </div>
    </div>
    </div>
    
  <!-- GCash QR Modal -->
  <div id="gcashModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; text-align: center; border-radius: 8px;">
      <span class="close" onclick="closeGcashModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
      <h2>Scan to Pay via GCash</h2>
      <p style="margin-bottom: 20px;">Please scan the QR code below to proceed with your payment.</p>
      <img src="../qrcode/2gcash.jfif" alt="GCash QR Code" style="max-width: 100%; height: auto; border: 2px solid #ccc; border-radius: 10px;">
      <p style="margin-top: 20px; font-weight: bold; color: #ff0000;">Total Amount: ₱<span id="gcash-modal-total">0.00</span></p>
      <button onclick="confirmGcashPayment()" style="margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">I have paid</button>
    </div>
  </div>

  <!-- Receipt Modal -->
  <div id="receiptModal" class="modal">
    <div class="receipt-content">
      <div class="receipt-header">
        <h2>Order Receipt</h2>
        <p>Bea Tea Coffee</p>
        <p id="receipt-date"></p>
        <div class="receipt-customer-info" style="text-align: left; margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 10px; font-size: 0.9em;">
          <p><strong>Customer:</strong> <span id="receipt-customer-name"></span></p>
          <p><strong>Phone:</strong> <span id="receipt-customer-phone"></span></p>
          <p><strong>Address:</strong> <span id="receipt-customer-address"></span></p>
        </div>
      </div>
      <div class="receipt-body" id="receipt-items">
        <!-- Items will be injected here -->
      </div>
      <div class="receipt-row">
        <span>Payment Method:</span>
        <span id="receipt-payment"></span>
      </div>
      <div class="receipt-total">
        <span>TOTAL:</span>
        <span id="receipt-total-amount">₱0.00</span>
      </div>
      <div class="receipt-footer">
        <p class="transaction-id">Ref: <span id="receipt-ref"></span></p>
        <button class="close-receipt-btn" onclick="closeReceiptModal()">Close & View Orders</button>
      </div>
    </div>
  </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-container">
      <div class="footer-column">
        <h2 class="footer-title">
          <img src="images/logo_btc.png" alt="Bea Tea Coffee Logo" class="footer-logo">
          Bea Tea Coffee
        </h2>
      </div>
      <div class="footer-column">
        <h3>Get in Touch</h3>
        <div class="social-links">
          <a href="https://www.facebook.com/bee.tea.coffee.main" class="social-link"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/beeteacoffee_?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-link"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      <div class="footer-column">
        <h3>Explore</h3>
        <ul>
          <li><a href="homecus.html">Home</a></li>
          <li><a href="products.html">Products</a></li>
          <li><a href="gallery.html">Gallery</a></li>
          <li><a href="about.html">About</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 Bea Tea Coffee. All Rights Reserved.</p>
    </div>
  </footer>

  <!-- SCRIPTS -->
  <!-- Note: We are keeping profile.js for client-side interactions (tabs, cart) 
       but we should remove the part that tries to fetch profile data from API -->
  <script src="profile.js?v=<?php echo time(); ?>"></script>
  <script src="mainest.js"></script>
  <script>
    function removeCartItem(cartId) {
      if (!confirm("Are you sure you want to remove this item?")) return;

      fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ cart_id: cartId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Item removed');
          location.reload(); // Reload to update list
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(err => {
        console.error(err);
        alert('An error occurred');
      });

      // Phone Number Formatter: 09XX-XXX-YYYY
      function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, ''); // Remove non-digits
        if (value.length > 11) value = value.slice(0, 11);
        
        let formatted = '';
        if (value.length > 0) {
          formatted = value.substring(0, 4);
          if (value.length > 4) {
            formatted += '-' + value.substring(4, 7);
          }
          if (value.length > 7) {
            formatted += '-' + value.substring(7, 11);
          }
        }
        input.value = formatted;
      }
    }
  </script>
</body>
</html>
