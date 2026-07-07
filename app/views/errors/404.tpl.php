<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вруб - о нас</title>
    <link rel="stylesheet" href="<?php echo CSS . '/index.css?dev=' . time(); ?>">
</head>
<body>
    <div class="container"> 
        <header class="header"> 
            <img src="<?php echo IMAGES . '/logo_header.png'?>" alt="" class="header__logo">
            <a href="/"><button class="header__button"> На главную </button></a>
        </header>

        <section class="hero"> 
            <div class="hero__texts">
                <h2 class="hero__title"> 404 </h2>
                <p class="hero__subtitle"> Данная страница не существует </p>
            </div>

            <div class="hero__bigLogo"> 
                <img src="<?php echo IMAGES . '/logo_big.png'?>" alt="">
            </div>
        </section>
    </div>
</body>