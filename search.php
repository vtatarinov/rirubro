<?php
include_once "db.php";

$area_min = (int)$_POST['area_min'];
$area_max = (int)$_POST['area_max'];
$rate_min = (int)$_POST['rate_min'];
$rate_max = (int)$_POST['rate_max'];
$offset = (isset($_POST['offset'])) ? (int)$_POST['offset'] : '';

$result_objects = $connect->query("SELECT * FROM blocks INNER JOIN objects ON blocks.object_id = objects.id_object WHERE area >= $area_min AND area <= $area_max AND rate_year >= $rate_min AND rate_year <= $rate_max GROUP BY object_id");
$objects = $result_objects->fetchAll();

if ($result_objects->rowCount() == 0) {
    echo '<p>По вашим параметрам ничего не найдено</p>';
    return;
}

foreach ($objects as $object) {
    $result_blocks = $connect->query("SELECT * FROM blocks WHERE area >= $area_min AND area <= $area_max AND rate_year >= $rate_min AND rate_year <= $rate_max AND object_id = $object[object_id] ORDER BY floor");
    $blocks = $result_blocks->fetchAll();
    
    $rowCount = $result_blocks->rowCount();
    $limit = $rowCount - $offset;


    if ($offset > 0) {
        $result_blocks = $connect->query("SELECT * FROM blocks WHERE area >= $area_min AND area <= $area_max AND rate_year >= $rate_min AND rate_year <= $rate_max AND object_id = $_POST[object_id] ORDER BY floor LIMIT $limit OFFSET $offset");
        $blocks = $result_blocks->fetchAll();
        renderBlocks($blocks);
        return;
    }

    if ($rowCount > 3) {
        $items = 3;
    } else {
        $items = $rowCount;
    }

    echo '<div class="item">
            <div class="item-img">
                <img src="'.$object['photopath'].'" alt="'.$object['photoalt'].'">
            </div>
            <div class="item-info">
                <a href="'.$object['url'].'" class="item-title">'.$object['name'].'<i class="far fa-building"></i></a>
                <p class="item-address">'.$object['address'].'</p>';
                //foreach ($blocks as $block) {
                renderBlocks($blocks);
                /* for ($i = 0; $i < $items; $i++) {
                    echo '<div class="item-block">
                            <p><b>Аренда офиса</b>&ensp;&bull;&ensp;<span>'.$blocks[$i]['floor'].' этаж</span>&ensp;&bull;&ensp;<span>Готово к въезду</span>&ensp;&bull;&ensp;<span>Смешанная планировка</span></p>
                            <div class="item-area"><a href="#">'.$blocks[$i]['area'].' м&sup2;</a></div>
                            <div class="item-rate-year">'.$blocks[$i]['rate_year'].' <span>руб./м&sup2; в год</span></div>
                            <div class="item-rate-month">'.$blocks[$i]['rate_month'].' <span>руб./мес</span></div>
                        </div>';
                } */
                if ($rowCount > 3) {
                    $more = $rowCount - 3;
                    echo '<a href="javascript:void(0);" class="item-showmore" onclick="showMore()" name="'.$object['id_object'].'">Ещё '.$more.' помещений в этом здании<i class="fas fa-chevron-down"></i></a>';
                }
    echo    '</div>
        </div>';
}

function renderBlocks($blocks)
{
    global $items;
    global $limit;
    global $offset;
    if ($offset > 0)
        $items = $limit;
    for ($i = 0; $i < $items; $i++) {
        echo '<div class="item-block">
                <p><b>Аренда офиса</b>&ensp;&bull;&ensp;<span>'.$blocks[$i]['floor'].' этаж</span>&ensp;&bull;&ensp;<span>Готово к въезду</span>&ensp;&bull;&ensp;<span>Смешанная планировка</span></p>
                <div class="item-area"><a href="#">'.$blocks[$i]['area'].' м&sup2;</a></div>
                <div class="item-rate-year">'.$blocks[$i]['rate_year'].' <span>руб./м&sup2; в год</span></div>
                <div class="item-rate-month">'.$blocks[$i]['rate_month'].' <span>руб./мес</span></div>
            </div>';
    }
}
