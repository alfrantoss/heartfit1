<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Order - {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 2mm;
            size: 80mm auto;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            padding: 3mm;
        }
        
        .receipt {
            width: 100%;
            max-width: 72mm;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            padding-bottom: 4px;
            margin-bottom: 6px;
            border-bottom: 2px dashed #000;
        }
        
        .header .brand {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .header .subtitle {
            font-size: 8px;
            letter-spacing: 1px;
        }
        
        .section {
            margin-bottom: 6px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 1.5px 0;
            font-size: 9px;
        }
        
        .info-label {
            font-weight: bold;
            white-space: nowrap;
        }
        
        .info-value {
            text-align: right;
            word-break: break-word;
            max-width: 60%;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        
        .divider-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }
        
        .package-box {
            padding: 4px 0;
            text-align: center;
        }
        
        .package-name {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .package-meta {
            font-size: 8px;
        }
        
        .menu-list {
            padding: 3px 0;
        }
        
        .menu-item {
            padding: 1.5px 0;
            font-size: 8px;
            border-bottom: 1px dotted #ccc;
            display: flex;
            justify-content: space-between;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .menu-more {
            text-align: center;
            font-size: 8px;
            padding: 2px 0;
        }
        
        .dates-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px;
            font-size: 8px;
        }
        
        .date-range {
            font-size: 8px;
        }
        
        .notes-box {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 8px;
            font-style: italic;
            background: #fafafa;
        }
        
        .total-box {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin: 6px 0;
            text-align: center;
        }
        
        .total-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        
        .total-amount {
            font-size: 14px;
            font-weight: bold;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending { background: #fff; }
        .status-unpaid { background: #fff; }
        .status-paid { background: #000; color: #fff; }
        .status-processing { background: #e0e0e0; }
        .status-completed { background: #000; color: #fff; }
        .status-expired { background: #fff; }
        .status-canceled { background: #fff; }
        
        .payment-info {
            font-size: 8px;
            text-align: center;
            margin-top: 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px dashed #000;
            font-size: 8px;
        }
        
        .footer .thanks {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        
        .qr-area {
            text-align: center;
            margin: 6px 0;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="brand">HEARTFIT</div>
            <div class="subtitle">STRUK PEMESANAN</div>
        </div>
        
        <div class="section">
            <div class="info-row">
                <span class="info-label">NO. ORDER</span>
                <span class="info-value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">TANGGAL</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">STATUS</span>
                <span class="info-value">
                    <span class="status-badge status-{{ strtolower($order->status) }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">CUSTOMER</span>
                <span class="info-value">{{ strtoupper($order->user->name ?? '-') }}</span>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="section package-box">
            <div class="package-name">{{ strtoupper($order->package_label ?? '-') }}</div>
            <div class="package-meta">
                {{ ucfirst($order->package_category ?? '-') }} &nbsp;|&nbsp; {{ $order->days ?? 0 }} HARI
            </div>
            @if($order->package_batch)
            <div class="package-meta">BATCH {{ $order->package_batch }}</div>
            @endif
        </div>
        
        <div class="divider"></div>
        
        @if($order->unique_menus && count($order->unique_menus) > 0)
        <div class="section">
            <div class="info-row">
                <span class="info-label">MENU</span>
            </div>
            <div class="menu-list">
                @foreach(array_slice($order->unique_menus, 0, 5) as $menu)
                    <div class="menu-item">
                        <span>&bull; {{ $menu }}</span>
                    </div>
                @endforeach
                @if(count($order->unique_menus) > 5)
                    <div class="menu-more">... +{{ count($order->unique_menus) - 5 }} MENU LAIN</div>
                @endif
            </div>
        </div>
        @endif
        
        @if($order->start_date && $order->end_date)
        <div class="section">
            <div class="info-row">
                <span class="info-label">PERIODE</span>
            </div>
            <div class="date-range">
                {{ \Carbon\Carbon::parse($order->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($order->end_date)->format('d/m/Y') }}
            </div>
            <div class="dates-grid">
                @foreach(array_slice($order->service_dates, 0, 5) as $date)
                    <div>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
                @endforeach
                @if(count($order->service_dates) > 5)
                    <div style="text-align:right;">+{{ count($order->service_dates) - 5 }}</div>
                @endif
            </div>
        </div>
        @endif
        
        @if(!empty($order->notes) && strcasecmp($order->package_category ?? '', 'personal') === 0)
        <div class="section">
            <div class="info-row">
                <span class="info-label">CATATAN</span>
            </div>
            <div class="notes-box">{{ $order->notes }}</div>
        </div>
        @endif
        
        <div class="total-box">
            <div class="total-label">Total Bayar</div>
            <div class="total-amount">Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</div>
        </div>
        
        <div class="section payment-info">
            <div class="info-row">
                <span class="info-label">METODE</span>
                <span class="info-value">{{ strtoupper($order->payment_method ?? '-') }}</span>
            </div>
            @if($order->paid_at)
            <div class="info-row">
                <span class="info-label">BAYAR</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($order->paid_at)->format('d/m/Y H:i') }}</span>
            </div>
            @endif
        </div>
        
        <div class="qr-area">
            @if($order->paid_at && $order->payment_method === 'qris')
            [QRIS]
            @endif
        </div>
        
        <div class="footer">
            <div class="thanks">TERIMA KASIH</div>
            <div>HEARTFIT NUTRITION</div>
            <div style="margin-top:3px;">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>