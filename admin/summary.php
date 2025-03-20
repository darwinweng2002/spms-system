
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel-Like Table</title>
    <style>
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            position: relative;
        }
        th {
            background-color: #2C3E50;
            color: #fff;
        }
        input, textarea {
            width: 100%;
            min-height: 30px;
            resize: both;  /* Enables resizing */
            overflow: auto;
            box-sizing: border-box;
        }
        .resizer {
            position: absolute;
            top: 0;
            right: -5px;
            width: 10px;
            height: 100%;
            cursor: col-resize;
        }
    </style>
</head>
<body>
<h2>Summary of Property Accountabilities</h2>

<form action="save_property_summary.php" method="POST">
    <label>Entity Name:</label>
    <input type="text" name="entity_name" required>

    <label>Accountable Officer:</label>
    <input type="text" name="accountable_officer" required>

    <label>Office/Department:</label>
    <input type="text" name="office_department" required>

    <label>Campus:</label>
    <input type="text" name="campus" required>
<table>
    <thead>
        <tr>
            <th>Reference No./Date</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Article</th>
            <th>Description</th>
            <th>Property/Inventory No.</th>
            <th>Date</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
            <th>Fund Cluster</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><input type="text"></td>
            <td><input type="number"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><textarea></textarea></td>
            <td><input type="text"></td>
            <td><input type="date"></td>
            <td><input type="number" step="0.01"></td>
            <td><input type="number" step="0.01"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
        </tr>
    </tbody>
</table>

<script>
    // Column resizing logic for all fields
    document.querySelectorAll('th').forEach(th => {
        const resizer = document.createElement('div');
        resizer.classList.add('resizer');
        th.appendChild(resizer);

        resizer.addEventListener('mousedown', function (e) {
            const startX = e.clientX;
            const startWidth = th.offsetWidth;

            function resizeHandler(e) {
                const newWidth = startWidth + (e.clientX - startX);
                th.style.width = `${newWidth}px`;
                th.querySelectorAll('input, textarea').forEach(input => {
                    input.style.width = `${newWidth}px`;
                });
            }

            function stopResize() {
                window.removeEventListener('mousemove', resizeHandler);
                window.removeEventListener('mouseup', stopResize);
            }

            window.addEventListener('mousemove', resizeHandler);
            window.addEventListener('mouseup', stopResize);
        });
    });

    // Arrow key navigation logic
    document.addEventListener('keydown', function (e) {
        const inputs = Array.from(document.querySelectorAll('input, textarea'));
        const currentIndex = inputs.indexOf(document.activeElement);

        switch (e.key) {
            case 'ArrowRight':
                if (currentIndex !== -1 && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
                break;
            case 'ArrowLeft':
                if (currentIndex !== -1 && currentIndex > 0) {
                    inputs[currentIndex - 1].focus();
                }
                break;
            case 'ArrowDown':
                if (currentIndex !== -1 && currentIndex + 11 < inputs.length) {
                    inputs[currentIndex + 11].focus();
                }
                break;
            case 'ArrowUp':
                if (currentIndex !== -1 && currentIndex - 11 >= 0) {
                    inputs[currentIndex - 11].focus();
                }
                break;
        }
    });
</script>

</body>
</html>
