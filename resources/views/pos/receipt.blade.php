<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            font-size: 12px;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .store-info {
            font-size: 10px;
            line-height: 1.4;
        }
        
        .receipt-body {
            margin: 10px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        
        .items-table td {
            padding: 5px 0;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .total-row.grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .receipt-footer {
            text-align: center;
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 10px;
        }
        
        .thank-you {
            font-weight: bold;
            margin: 10px 0;
        }
        
        .print-button {
            width: 100%;
            padding: 15px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        
        .print-button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="receipt-header">
            <div class="store-name">POS SYSTEM</div>
            <div class="store-info">
                Jl. Contoh No. 123, Bandung<br>
                Telp: (022) 1234567<br>
                www.possystem.com
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="receipt-body">
            <div class="info-row">
                <span>Invoice:</span>
                <strong>{{ $transaction->invoice_number }}</strong>
            </div>
            <div class="info-row">
                <span>Tanggal:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span>{{ $transaction->user->name }}</span>
            </div>
            @if($transaction->customer_name)
                <div class="info-row">
                    <span>Customer:</span>
                    <span>{{ $transaction->customer_name }}</span>
                </div>
            @endif
        </div>

        <!-- Items -->
        <table class="items-table">
            <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td colspan="2">
                            <div class="item-name">{{ $item->product_name }}</div>
                            <div class="item-detail">
                                <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($transaction->discount_amount > 0)
                <div class="total-row">
                    <span>Diskon 
                        @if($transaction->discount_type === 'percentage')
                            ({{ $transaction->discount_value }}%)
                        @endif:
                    </span>
                    <span>- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="total-row">
                <span>Pajak ({{ $transaction->tax_rate }}%):</span>
                <span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
            </div>

            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
            </div>

            <div class="total-row">
                <span>Bayar ({{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}):</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>

            @if($transaction->change_amount > 0)
                <div class="total-row">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <div class="thank-you">TERIMA KASIH</div>
            <div>Barang yang sudah dibeli<br>tidak dapat dikembalikan</div>
            <div style="margin-top: 10px;">{{ now()->format('d M Y H:i:s') }}</div>
        </div>
    </div>

    <!-- Print Button (Hidden saat print) -->
    <div class="no-print">
        <button class="print-button" onclick="window.print()">
            🖨️ Print Struk
        </button>
        <button class="print-button" style="background: #6b7280; margin-top: 10px;" onclick="window.close()">
            ✖️ Tutup
        </button>
    </div>

    <script>
        // Auto print saat halaman dibuka
        window.onload = function() {
            // Uncomment jika mau auto print
            // window.print();
        }
    </script>
</body>
</html>