<?php
namespace common\components\core;

use common\modules\chat\Chat;
use common\modules\editor\Editor;
use common\modules\game\Game;
use common\modules\homeBackend\HomeBackend;
use common\modules\homeFrontend\HomeFrontend;
use common\modules\image\Image;
use common\modules\imageUploader\ImageUploader;
use common\modules\page\Page;
use common\modules\pageCodeInjection\PageCodeInjection;
use common\modules\profile\Profile;
use common\modules\sale\Sale;
use common\modules\scheduler\Scheduler;
use common\modules\seo\Seo;
use common\modules\telegram\Telegram;
use common\modules\user\User;
use common\modules\yandexMoney\YandexMoney;

/**
 * @property-read Editor            $editor            Модуль редактора
 * @property-read Chat              $chat              Модуль чата
 * @property-read Game              $game              Модуль игр
 * @property-read HomeFrontend      $homeFrontend      Модуль домашней страницы для фронтэнда
 * @property-read HomeBackend       $homeBackend       Модуль домашней страницы для бэкэнда
 * @property-read Image             $image             Модуль изображений
 * @property-read ImageUploader     $imageUploader     Модуль загрузки изображений через веб-интерфейс
 * @property-read Page              $page              Модуль статических страниц
 * @property-read PageCodeInjection $pageCodeInjection Модуль инъекции кода на страницах
 * @property-read Profile           $profile           Модуль профиля
 * @property-read Sale              $sale              Модуль продажи
 * @property-read Scheduler         $scheduler         Модуль "Планировщик"
 * @property-read Seo               $seo               Модуль "СЕО"
 * @property-read Telegram          $telegram          Модуль для взаимодействия с телеграмом
 * @property-read User              $user              Модуль пользователей
 * @property-read YandexMoney       $yandexMoney       Модуль Яндекс.Денег
 */
class ModuleManagerModules extends \yiiCustom\core\ModuleManagerModules {}