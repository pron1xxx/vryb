<?php require VIEWS . '/incs/headers/mainheader.tpl.php' ?>
<form action="/search" class="search" id="search_form" title="Поиск по названию и описанию">
    <input type="text" class="search__input" name="search" placeholder="Поиск" id="search_input">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <button class="search__button" type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor" d="M9.5 16q-2.725 0-4.612-1.888T3 9.5t1.888-4.612T9.5 3t4.613 1.888T16 9.5q0 1.1-.35 2.075T14.7 13.3l5.6 5.6q.275.275.275.7t-.275.7t-.7.275t-.7-.275l-5.6-5.6q-.75.6-1.725.95T9.5 16m0-2q1.875 0 3.188-1.312T14 9.5t-1.312-3.187T9.5 5T6.313 6.313T5 9.5t1.313 3.188T9.5 14" />
        </svg>
    </button>
</form>
<ul class="categories">
    <?php foreach($categories as $category): ?>
    <li> <button class="categories__category"><?= $category['category_name'] ?></button> </li>
    <?php endforeach; ?>
    <button id="reset_categories" style="color: white"> Сбросить категории </button>
</ul>

<div class="courses" id="courses">
    <?php foreach ($courses as $course): ?>
        <div class="course" data-category="<?=  $course['category'] ?>">
            <a href="/course/show/?id=<?= htmlspecialchars($course['id']) ?>">
                <div class="course__preview"> <img src="<?= htmlspecialchars($course['preview_url']) ?>" alt="course_preview"> </div>
            </a>
            <h2 class="course__title"> <?= htmlspecialchars($course['course_name']) ?> </h2>
            <div class="course__authorWrapper">
                <a href="/channel/?id=<?= htmlspecialchars($course['author_id']) ?>" class="course__author"> <?= htmlspecialchars($course['channel_name']) ?> </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<form action="/main/show/more" method="post" id="show_more">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <input type="hidden" name="counts_courses" id="count" value="2">
    <input type="hidden" name="search_str" id="search_str">
    <button type="submit" class="moreButton"> Показать еще </button>
</form>
<?php require VIEWS . '/incs/headers/mainfooter.tpl.php' ?>


<script src="/public/assets/js/show_more.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/search_main.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/category_filter.js?v=<?= time() ?>"> </script>
<script src="/public/assets/js/show_message.js?v=<?= time() ?>"> </script>