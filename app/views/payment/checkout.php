<?php require_once APP_ROOT . '/app/views/inc/header.php'; ?>

<style>
    .payment-container { display: flex; gap: 30px; flex-wrap: wrap; margin-top: 20px;}
    .order-summary { flex: 1; min-width: 300px; background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #ddd; }
    .payment-form { flex: 1; min-width: 300px; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .line-item { display: flex; justify-content: space-between; margin-bottom: 10px; color: #555; }
    .total-line { display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid #ccc; font-weight: bold; font-size: 1.2em; color: #333; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9em; }
    .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: monospace; }
</style>

<div class="card">
    <h2 style="margin-top: 0; color: #007bff;">Secure Checkout</h2>
    <p style="color: #666; margin-bottom: 30px;">Demo Environment - No real cards will be charged.</p>

    <div class="payment-container">

        <!-- Order Summary -->
        <div class="order-summary">
            <h3 style="margin-top:0;">Order Summary</h3>
            <p style="font-weight: bold;"><?= Security::escape($data['title']) ?></p>
            <p style="font-size: 0.9em; color: #666; margin-bottom: 20px;"><?= Security::escape($data['description']) ?></p>

            <div class="line-item">
                <span>Base Amount:</span>
                <span>₹<?= number_format($data['base_amount'], 2) ?></span>
            </div>
            <div class="line-item">
                <span>Service Tax (5%):</span>
                <span>₹<?= number_format($data['tax_amount'], 2) ?></span>
            </div>
            <div class="line-item" style="font-size: 0.85em; font-style: italic;">
                <span>Subtotal:</span>
                <span>₹<?= number_format($data['base_amount'] + $data['tax_amount'], 2) ?></span>
            </div>
            <div class="line-item">
                <span>Platform Commission (5%):</span>
                <span>₹<?= number_format($data['platform_fee'], 2) ?></span>
            </div>

            <div class="total-line">
                <span>Total to Pay:</span>
                <span>₹<?= number_format($data['total_amount'], 2) ?></span>
            </div>
        </div>

        <!-- Dummy Payment Form -->
        <div class="payment-form">
            <h3 style="margin-top:0;">Payment Details</h3>

            <div class="form-group">
                <label>Card Number</label>
                <input type="text" placeholder="XXXX XXXX XXXX XXXX" value="4242 4242 4242 4242">
            </div>
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Expiry (MM/YY)</label>
                    <input type="text" placeholder="12/25" value="12/26">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CVV</label>
                    <input type="text" placeholder="123" value="123">
                </div>
            </div>
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" placeholder="John Doe" value="Demo User">
            </div>

            <form action="<?= URL_ROOT ?>/payment/process" method="POST" style="margin-top: 30px;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="type" value="<?= Security::escape($data['type']) ?>">
                <input type="hidden" name="ref_id" value="<?= Security::escape($data['ref_id']) ?>">
                <input type="hidden" name="base_amount" value="<?= $data['base_amount'] ?>">
                <input type="hidden" name="tax_amount" value="<?= $data['tax_amount'] ?>">
                <input type="hidden" name="platform_fee" value="<?= $data['platform_fee'] ?>">
                <input type="hidden" name="total_amount" value="<?= $data['total_amount'] ?>">

                <button type="submit" name="payment_status" value="success" class="btn-primary" style="background: #28a745; width: 100%; border:none; padding: 12px; font-size: 1.1em; cursor: pointer; margin-bottom: 10px;">
                    Simulate Successful Payment
                </button>
                <button type="submit" name="payment_status" value="failed" class="btn-outline" style="border-color: #dc3545; color: #dc3545 !important; width: 100%; padding: 10px; cursor: pointer; background: transparent;">
                    Simulate Failed Payment
                </button>
            </form>
        </div>

    </div>
</div>

<?php require_once APP_ROOT . '/app/views/inc/footer.php'; ?>
