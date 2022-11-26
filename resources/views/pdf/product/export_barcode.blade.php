<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Fortamil</title>
    <style>
        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>

<body>
    <div>
        <table id="customers">
            <tr>
                <th class="col col-1">#ID</th>
                <th class="col col-2">Nama Produk</th>
                <th class="col col-3">SKU</th>
                <th class="col col-4">Barcode</th>
            </tr>
            @foreach ($products as $p)
                <tr class="table-row">
                    <td class="col col-1">{{ $p->id }}</td>
                    <td class="col col-2">{{ $p->name }}</td>
                    <td class="col col-3">{{ $p->sku }}</td>
                    <td class="col col-4"><img src="{{ $p->barcode_url }}" alt="barcode-{{ $p->id }}"></td>
                </tr>
            @endforeach
        </table>
    </div>
</body>

</html>
