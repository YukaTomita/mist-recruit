<!DOCTYPE html>
<html>
<head>
    <title>Voting Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="position: relative; width: 80%; margin: auto;">
        <canvas id="voteChart" height="600px" width="700px"></canvas>
    </div>

    <script>
        // データの取得
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "root";
        $dbname = "enterprise";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "
            SELECT o.name AS option_name, COUNT(v.id) AS vote_count
            FROM options o
            LEFT JOIN votes v ON o.id = v.option_id
            GROUP BY o.id
            ORDER BY vote_count DESC;
        ";

        $result = $conn->query($sql);
        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    "option_name" => $row["option_name"],
                    "vote_count" => $row["vote_count"]
                );
            }
        }

        $conn->close();
        ?>

        // データの設定
        var data = {
            labels: <?php echo json_encode(array_column($data, "option_name")); ?>.map((v) => v.replace('ー', '丨').split("")),
            datasets: [{
                data: <?php echo json_encode(array_column($data, "vote_count")); ?>,
                backgroundColor: [
                    <?php
                    for ($i = 0; $i < count($data); $i++) {
                        if ($i < 3) {
                            echo "'#8B2022',";
                        } else {
                            echo "'rgba(139, 32, 34, 0.5)',";
                        }
                    }
                    ?>
                ],
                borderWidth: 0 // 区切り線を非表示
            }]
        };

        // グラフ作成
        var ctx = document.getElementById('voteChart').getContext('2d');
        var voteChart = new Chart(ctx, {
            type: 'bar', // 縦棒グラフ
            data: data,
            options: {
                scales: {
                    x: {
                        display: true, // X軸目盛り表示
                    },
                    y: {
                        display: false, // Y軸目盛り非表示
                    }
                },
                plugins: {
                    legend: {
                        display: false, // 凡例非表示
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20
                    }
                },
                indexAxis: 'x', // 横軸に表示
            }
        });

        // 項目名の下に画像を挿入
        Chart.register({
            afterDraw: function(chart, args, options) {
                var ctx = chart.ctx;
                ctx.save();
                var xAxis = chart.scales['x'];
                var yAxis = chart.scales['y'];
                var datasets = chart.data.datasets;

                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 12px Arial';

                datasets.forEach(function(dataset, datasetIndex) {
                    var meta = chart.getDatasetMeta(datasetIndex);
                    if (!meta.hidden) {
                        meta.data.forEach(function(element, index) {
                            var dataString = dataset.data[index].toString();
                            var position = element.tooltipPosition();
                            ctx.fillStyle = '#333'; // 項目名のテキスト色
                            ctx.fillText(dataString, position.x, position.y - 10); // 高さを調整

                            if (index < 3) {
                                var img = new Image();
                                img.src = 'img/ex-' + (index + 1) + '.png'; // 画像のパスを設定
                                var imgWidth = 20; // 画像の幅
                                var imgHeight = 20; // 画像の高さ
                                ctx.drawImage(img, position.x - imgWidth / 2, position.y + 10, imgWidth, imgHeight);
                            }
                        });
                    }
                });

                ctx.restore();
            }
        });
    </script>

<?php if (!$voteHistory) : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php foreach ($options as $option) : ?>
                    <div class="option">
                        <input type="checkbox" id="option<?php echo $option['id']; ?>" name="vote[]" value="<?php echo $option['id']; ?>">
                        <label for="option<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label>
                    </div>
                <?php endforeach; ?>
                <button class="post-btn" type="submit">投票する</button>
            </form>
        <?php else : ?>
            <p class="vote-message asterisk">※すでに投票済みです。</p>
        <?php endif; ?>
    </div>

</body>
</html>
