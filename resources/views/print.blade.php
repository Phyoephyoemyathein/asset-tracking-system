<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Inventory Report</title>
    <style>
        * {
            box-sizing: border-box !important;
            margin: 0;
            padding: 0;
        }

        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            color: #000; 
            background: #fff;
        }

        h2 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        /* Table ကိုယ်တိုင်မှာ Border အပြည့်အစုံ အသေအချာဖြစ်စေရန် */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            border-spacing: 0 !important;
            border: 2px solid #000000 !important;
        }
        
        th, td { 
            border: 1px solid #000000 !important; 
            padding: 9px 8px !important; 
            text-align: left; 
            vertical-align: middle; 
            font-size: 13px;
            word-wrap: break-word;
        }
        
        th { 
            background-color: #f3f4f6 !important; 
            font-weight: bold; 
            text-align: center; 
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact; 
        }

        .text-center {
            text-align: center !important;
        }

        /* ကော်လံအကျယ်များကို ဒုတိယပုံစံအတိုင်း ညီညာစွာ ခွဲပေးခြင်း */
        .col-no { width: 50px; text-align: center; }
        .col-name { width: 140px; }
        .col-sku { width: 130px; }
        .col-cat { width: 120px; }
        .col-wh { width: 110px; }
        .col-qty { width: 80px; text-align: center; }
        .col-qr { width: 85px; text-align: center; }

        /* QR Code Layout */
        td.qr-cell {
            text-align: center !important;
        }

        td.qr-cell svg, 
        td.qr-cell img {
            width: 45px !important;
            height: 45px !important;
            display: block;
            margin: 0 auto;
        }

        /* Print Options သေချာစေရန် */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            table { border: 2px solid #000000 !important; }
            th, td { border: 1px solid #000000 !important; }
        }
    </style>
</head>
<body>

    <!-- Dashboard သို့ ပြန်ရန် ခလုတ် -->
    <div class="no-print" style="margin-bottom: 20px;">
        <a href="/dashboard" style="padding: 10px 20px; background-color: #64748b; color: white; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 14px;">⬅️ Back to Dashboard</a>
    </div>

    <h2>Asset Inventory & QR Codes</h2>
    
    <table>
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-name">Asset Name</th>
                <th class="col-sku">SKU</th>
                <th class="col-cat">Category</th>
                <th class="col-wh">Warehouse</th>
                <th class="col-qty">Quantity</th>
                <th class="col-qr">QR Code</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{$asset->name }}</td>
                <td>{{ $asset->sku }}</td>
                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                <td>{{ $asset->warehouse->name ?? 'N/A' }}</td>
                <td class="text-center">
                    <span style="font-weight: bold; color: #16a34a;">{{ $asset->available_items_count ?? 0 }}</span>
                    <span style="color: #9ca3af; margin: 0 2px;">/</span>
                    <span>{{ $asset->items_count ?? 0 }}</span>
                </td>
                <td class="qr-cell">
                    {!! $asset->qr_code !!}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        };

        window.addEventListener('afterprint', function () {
            window.location.href = "/dashboard";
        });

        window.onafterprint = function() {
            window.location.href = "/dashboard";
        };

        let mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (!mql.matches) {
                setTimeout(function() {
                    window.location.href = "/dashboard";
                }, 0);
            }
        });
    </script>
</body>
</html>