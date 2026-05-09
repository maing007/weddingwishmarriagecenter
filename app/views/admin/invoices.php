<?php
$title = "Invoices";
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/sidebar.php';
?>

<style>
.admin-content {
    background: #f5f5f5;
    min-height: 100vh;
    padding: 25px;
}

.page-title {
    font-size: 24px;
    font-weight: 600;
    color: #444;
    margin-bottom: 25px;
}

.invoice-card {
    background: #fff;
    padding: 25px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.table thead th {
    background: #fafafa;
    font-weight: 600;
    color: #555;
    border-bottom: 2px solid #ddd;
    padding: 14px;
}

.table td {
    vertical-align: middle;
    color: #666;
    padding: 14px;
}

.btn-download {
    background: #2196f3;
    border: none;
    color: white;
    padding: 7px 18px;
    font-size: 14px;
}

.btn-download:hover {
    background: #1976d2;
    color: white;
}

.amount {
    font-weight: 600;
    color: #2ecc71;
}

@media (max-width: 768px) {
    .invoice-card {
        padding: 15px;
    }

    .table {
        font-size: 13px;
    }
}
</style>

<div class="admin-content">

    <div class="page-title">Invoices</div>

    <div class="invoice-card">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($invoices as $i): ?>
                    <tr>
                        <td><?= $i['invoice_no'] ?></td>

                        <td>
                            <?= htmlspecialchars($i['first_name'] . ' ' . $i['last_name']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($i['package_name']) ?>
                        </td>

                        <td class="amount">
                            ₹ <?= number_format($i['amount'], 2) ?>
                        </td>

                        <td>
                            <?= date('d M Y', strtotime($i['created_at'])) ?>
                        </td>

                        <td>
                            <button class="btn btn-download"
                                    onclick="downloadInvoicePDF('invoice-<?= $i['id'] ?>')">
                                Download PDF
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- ================= INVOICE TEMPLATES ================= -->
<?php foreach ($invoices as $i): ?>
<div id="invoice-<?= $i['id'] ?>"
     style="position:absolute; left:-9999px; top:0; width:210mm; font-family:DejaVu Sans; font-size:12px;">

    <div style="text-align:center;">
        <img src="<?= BASE_URL ?>/assets/images/logo.png" style="width:120px; margin-bottom:10px;">
        <h2 style="margin:0;">Wedding Matrimony</h2>
        <p style="margin:5px 0;">www.weddingwishcenter.com</p>
    </div>

    <hr>

    <p>
        <strong>Invoice No:</strong> <?= $i['invoice_no'] ?><br>
        <strong>Date:</strong> <?= date('d M Y', strtotime($i['created_at'])) ?>
    </p>

    <p>
        <strong>Billed To:</strong><br>
        <?= htmlspecialchars($i['first_name'] . ' ' . $i['last_name']) ?><br>
        <?= htmlspecialchars($i['email']) ?>
    </p>

    <div style="margin-top:20px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px; background:#f5f5f5;">
                        Package
                    </th>
                    <th style="border:1px solid #ddd; padding:8px; background:#f5f5f5; text-align:right;">
                        Amount
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; padding:8px;">
                        <?= htmlspecialchars($i['package_name']) ?>
                    </td>
                    <td style="border:1px solid #ddd; padding:8px; text-align:right;">
                        ₹ <?= number_format($i['amount'], 2) ?>
                    </td>
                </tr>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px; text-align:right;">
                        Total
                    </th>
                    <th style="border:1px solid #ddd; padding:8px; text-align:right;">
                        ₹ <?= number_format($i['amount'], 2) ?>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>

    <p style="margin-top:30px; text-align:center;">
        Thank you for choosing our service.
    </p>
</div>
<?php endforeach; ?>

<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadInvoicePDF(invoiceId) {
    const element = document.getElementById(invoiceId);

    const opt = {
        margin: 0.5,
        filename: invoiceId + '-Invoice.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>