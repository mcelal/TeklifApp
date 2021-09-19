<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Teklif</title>

        <style type="text/css">
        .table {
            background-color: #eee;
            border-collapse: collapse;
        }

        .table th {
            background-color: #000;
            color: white;
            width: 50%;
        }

        .table td, .table th {
            padding: 5px;
            border: 1px solid #4d4d4d;
        }
        </style>
    </head>
    <body>
        <table style="width: 100%">
            <tr>
                <td colspan="4">
                    <h2>{{ $proposal->customer_title }}</h2>
                </td>
                <td style="text-align: right">{{ $proposal->created_at->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td colspan="5">
                    <table class="table" style="width: 100%; border: 1px solid black; border-collapse: collapse">
                        <thead>
                            <tr>
                                <th>Marka Model</th>
                                <th>Miktar</th>
                                <th>Fiyat</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal->items as $item)
                                <tr>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>

        </table>
    </body>
</html>
