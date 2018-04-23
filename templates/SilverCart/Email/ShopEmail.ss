<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-DE" lang="de-DE">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>{$ShopEmailSubject}</title>
    </head>
    <body>
        <style type="text/css">
            body { font-family: Verdana; font-size:70.00%; color:#666; }
            .text-right { text-align: right !important; }
            .text-left { text-align: left !important; }
            h1 { font-size: 14px; }
            h2 { font-size: 12px; }
            body, table td { font-size: 10px; }
            table { width: auto; border-collapse:collapse; margin-bottom:0.5em; border-top:0px; border-bottom:0px; }
            table caption { font-variant:small-caps; }
            table.full { width:100%; }
            table.fixed { table-layout:fixed; }

            th,td { padding: 0.2em 0.5em; }
            thead th { font-size: 10px; border-bottom:1px #666 solid; }
            tbody th { font-size: 10px; background:#e0e0e0; color:#666; border-bottom:1px solid #fff; text-align:left; }
            tbody th[scope="row"], tbody th.sub { background:#f0f0f0; }

            tbody th { border-bottom:1px solid #fff; text-align:left; }
            tbody td { border-bottom:1px solid #eee; }

            tfoot td {border-top: 1px #666 solid; }
        </style>
        <div style="width: 95%; max-width: 800px; margin: 0px auto;">
            {$Layout}
        </div>
    </body>
</html>