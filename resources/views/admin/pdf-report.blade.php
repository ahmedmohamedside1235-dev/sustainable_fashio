<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sustainability Report – SwapSustain</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #333;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 3px solid #198754;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 26px;
            color: #198754;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 13px;
            color: #888;
        }

        /* ── Stats Row ── */
        .stats-row {
            display: flex;
            gap: 16px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-box {
            flex: 1;
            min-width: 140px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
            border-top: 4px solid #198754;
        }

        .stat-box.orange { border-top-color: #f0a500; }
        .stat-box.blue   { border-top-color: #0d6efd; }
        .stat-box.red    { border-top-color: #e74c3c; }

        .stat-box h2 {
            font-size: 28px;
            font-weight: 800;
            color: #198754;
        }

        .stat-box.orange h2 { color: #f0a500; }
        .stat-box.blue   h2 { color: #0d6efd; }
        .stat-box.red    h2 { color: #e74c3c; }

        .stat-box p {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #198754;
            margin-bottom: 12px;
            border-left: 4px solid #198754;
            padding-left: 10px;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 13px;
        }

        thead {
            background: #f0faf0;
        }

        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: .4px;
            border-bottom: 2px solid #e0e0e0;
        }

        tbody tr { border-bottom: 1px solid #f5f5f5; }
        tbody td { padding: 10px 12px; color: #333; }

        /* ── Bar ── */
        .bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .bar-label { font-size: 12px; color: #555; width: 100px; flex-shrink: 0; }

        .bar-track {
            flex: 1;
            height: 10px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #198754;
            border-radius: 10px;
        }

        .bar-val {
            font-size: 12px;
            font-weight: 700;
            color: #198754;
            width: 40px;
            text-align: right;
        }

        /* ── Grid ── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }

        .report-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 16px;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 11px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 16px;
            margin-top: 20px;
        }

        /* ── Print button ── */
        .print-btn {
            display: block;
            width: 200px;
            margin: 0 auto 30px;
            padding: 10px;
            background: #198754;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
        }

        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">🖨️ Print / Save as PDF</button>

    <!-- Header -->
    <div class="header">
        <h1>♻ SwapSustain — Sustainability Report</h1>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <!-- Overview Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <h2>{{ $swapAccepted + $txAccepted }}</h2>
            <p>Items Reused</p>
        </div>
        <div class="stat-box blue">
            <h2>{{ $swapAccepted }}</h2>
            <p>Completed Swaps</p>
        </div>
        <div class="stat-box orange">
            <h2>{{ $txAccepted }}</h2>
            <p>Completed Requests</p>
        </div>
        <div class="stat-box red">
            <h2>{{ $totalItems }}</h2>
            <p>Total Items Listed</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid-2">

        <!-- Material Usage -->
        <div class="report-card">
            <div class="section-title">Material Usage</div>
            @forelse($materialStats as $mat)
                <div class="bar-row">
                    <span class="bar-label">{{ $mat->material_name }}</span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ round(($mat->items_count / $totalItems) * 100) }}%"></div>
                    </div>
                    <span class="bar-val">{{ $mat->items_count }}</span>
                </div>
            @empty
                <p style="color:#aaa;font-size:13px;">No data</p>
            @endforelse
        </div>

        <!-- Items by Category -->
        <div class="report-card">
            <div class="section-title">Items by Category</div>
            @forelse($categoryStats as $cat)
                <div class="bar-row">
                    <span class="bar-label">{{ ucfirst($cat->category) }}</span>
                    <div class="bar-track">
                        <div class="bar-fill" style="width:{{ round(($cat->count / $totalItems) * 100) }}%;background:#0d6efd;"></div>
                    </div>
                    <span class="bar-val" style="color:#0d6efd;">{{ $cat->count }}</span>
                </div>
            @empty
                <p style="color:#aaa;font-size:13px;">No data</p>
            @endforelse
        </div>

    </div>

    <!-- Transactions Table -->
    <div class="section-title">Transaction Status Summary</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>✅ Accepted</td>
                <td>{{ $txAccepted }}</td>
                <td>{{ round(($txAccepted / (max($txAccepted + $txPending + $txRejected, 1))) * 100) }}%</td>
            </tr>
            <tr>
                <td>⏳ Pending</td>
                <td>{{ $txPending }}</td>
                <td>{{ round(($txPending / (max($txAccepted + $txPending + $txRejected, 1))) * 100) }}%</td>
            </tr>
            <tr>
                <td>❌ Rejected</td>
                <td>{{ $txRejected }}</td>
                <td>{{ round(($txRejected / (max($txAccepted + $txPending + $txRejected, 1))) * 100) }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Swaps Table -->
    <div class="section-title">Swap Status Summary</div>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>✅ Accepted</td>
                <td>{{ $swapAccepted }}</td>
                <td>{{ round(($swapAccepted / (max($swapAccepted + $swapPending + $swapRejected, 1))) * 100) }}%</td>
            </tr>
            <tr>
                <td>⏳ Pending</td>
                <td>{{ $swapPending }}</td>
                <td>{{ round(($swapPending / (max($swapAccepted + $swapPending + $swapRejected, 1))) * 100) }}%</td>
            </tr>
            <tr>
                <td>❌ Rejected</td>
                <td>{{ $swapRejected }}</td>
                <td>{{ round(($swapRejected / (max($swapAccepted + $swapPending + $swapRejected, 1))) * 100) }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        SwapSustain — Sustainability Report — {{ now()->format('Y') }}
    </div>

</body>
</html>