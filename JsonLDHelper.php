<?php

namespace panix\engine;

use panix\mod\shop\models\Product;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Html;
use yii\helpers\Url;
use ML\JsonLD\JsonLD;

/**
 * Helper class for registering structured data markup in JSON-LD format
 * add in you head
 * \panix\engine\JsonLDHelper::addBreadcrumbList();
 * \panix\engine\JsonLDHelper::addOrganization([...]);
 * \panix\engine\JsonLDHelper::registerScripts();
 */
class JsonLDHelper extends BaseObject
{


    public static function addProduct(Product $model, $fakeRating = false)
    {
        if (!$fakeRating) {
            $reviewsQuery = $model->getReviews()->status(1);
            $reviewsCount = $reviewsQuery->roots()->count();
            $ratingScore = $model->ratingScore;
        } else {
            $range = range(4.5, 5, 0.1);
            $reviewsCount = rand(50, 60);
            $ratingScore = $range[rand(0, count($range)-1)];
        }

        if ($model->availability == Product::STATUS_IN_STOCK) { //Есть в наличии
            $availability = "https://schema.org/InStock";
        } elseif ($model->availability == Product::STATUS_OUT_STOCK || $model->availability == Product::STATUS_ARCHIVE) { //Нет в наличии
            $availability = "https://schema.org/OutOfStock";
        } elseif ($model->availability == Product::STATUS_PREORDER) { //предзаказ
            $availability = "https://schema.org/PreOrder";
        }


        $doc["@type"] = 'Product';
        $doc["http://schema.org/sku"] = $model->sku;
        $doc["http://schema.org/description"] = $model->full_description;
        $doc["http://schema.org/name"] = $model->name;
        if ($model->brand_id) {
            if ($model->brand) {
                $doc["http://schema.org/brand"] = (object)[
                    "@type" => "Brand",
                    "http://schema.org/name" => $model->brand->name
                ];
            }
        }
        foreach ($model->images as $image) {
            $original = $image->get(false, ['watermark' => false]);
            $doc["http://schema.org/image"][] = Url::to($original, true);
        }
        $doc["http://schema.org/aggregateRating"] = (object)[
            "@type" => "AggregateRating",
            "http://schema.org/ratingValue" => $ratingScore,
            "http://schema.org/reviewCount" => (int)$reviewsCount
        ];


        //"itemCondition": "https://schema.org/NewCondition",
        //"availability": "https://schema.org/InStock",
        $priceSpecification[] = (object)[

            "@type" => "http://schema.org/UnitPriceSpecification",
            "http://schema.org/priceCategory" => "https://schema.org/ListPrice",
            "http://schema.org/price" => (string)Yii::$app->currency->convert($model->price, $model->currency_id),
            "http://schema.org/priceCurrency" => Yii::$app->currency->main['iso']
        ];


        if ($model->hasDiscount) {
            $priceSpecification[] = (object)[
                "@type" => "http://schema.org/UnitPriceSpecification",
                "http://schema.org/priceCategory" => "https://schema.org/SalePrice",
                "http://schema.org/price" => (string)Yii::$app->currency->convert($model->discountPrice, $model->currency_id),
                "http://schema.org/priceCurrency" => Yii::$app->currency->main['iso'],
            ];
        }

        $doc["http://schema.org/offers"] = (object)[
            "@type" => "http://schema.org/Offer",
            "http://schema.org/itemCondition" => "https://schema.org/NewCondition",
            "http://schema.org/availability" => $availability,
            "http://schema.org/priceSpecification" => $priceSpecification
            //"http://schema.org/price" => (string)$model->getFrontPrice(),
            //"http://schema.org/priceCurrency" => Yii::$app->currency->main['iso'],

            //"offerCount": "5", //Количество предложений по товару. (disabel price param)
            //"lowPrice": "119.99",
            //"highPrice": "199.99",
        ];
        self::add($doc);
    }


    /**
     * Adds BreadcrumbList schema.org markup based on the application view `breadcrumbs` parameter
     */
    public static function addBreadcrumbList()
    {
        $view = Yii::$app->getView();

        $breadcrumbList = [];
        if (isset($view->params['breadcrumbs'])) {
            $position = 1;
            foreach ($view->params['breadcrumbs'] as $breadcrumb) {
                if (is_array($breadcrumb)) {
                    $breadcrumbList[] = (object)[
                        "@type" => "http://schema.org/ListItem",
                        "http://schema.org/position" => $position,
                        "http://schema.org/item" => (object)[
                            "@id" => Url::to($breadcrumb['url'], true),
                            "http://schema.org/name" => $breadcrumb['label'],
                        ]
                    ];
                } else {
                    // Is it ok to omit URL here or not? Google is not clear on that:
                    // http://stackoverflow.com/questions/33688608/how-to-markup-the-last-non-linking-item-in-breadcrumbs-list-using-json-ld
                    $breadcrumbList[] = (object)[
                        "@type" => "http://schema.org/ListItem",
                        "http://schema.org/position" => $position,
                        "http://schema.org/item" => (object)[
                            "http://schema.org/name" => $breadcrumb,
                        ]
                    ];
                }
                $position++;
            }
        }

        $doc = (object)[
            "@type" => "http://schema.org/BreadcrumbList",
            "http://schema.org/itemListElement" => $breadcrumbList
        ];

        self::add($doc);
    }

    /**
     * Organization
     *
     * @param array $options
     *
     * @param array $address Address List 'address'=>[['addressLocality' => '','postalCode' => '','streetAddress' => ''],[...]]
     */
    public static function addOrganization(array $options = [])
    {

        if (isset($options['address']) && is_array($options['address'])) {
            $addressList = [];
            foreach ($options['address'] as $address) {
                if (isset($address['addressLocality'], $address['postalCode'], $address['streetAddress'])) {
                    $addressList[] = (object)[
                        "@type" => "http://schema.org/PostalAddress",
                        "http://schema.org/addressLocality" => $address['addressLocality'],
                        "http://schema.org/postalCode" => $address['postalCode'],
                        "http://schema.org/streetAddress" => $address['streetAddress'],
                    ];

                }
            }

            $doc["http://schema.org/address"] = $addressList;
        }

        if (isset($options['contactPoint']) && is_array($options['contactPoint'])) {
            $contactPointList = [];
            foreach ($options['contactPoint'] as $contact_key => $contact) {
                $item = [];

                $item["@type"] = "http://schema.org/ContactPoint";
                if (isset($contact['phone']))
                    $item["http://schema.org/telephone"] = $contact['phone'];

                if (isset($contact['contactType']))
                    $item["http://schema.org/contactType"] = $contact['contactType'];

                if (isset($contact['contactOption']))
                    $item["http://schema.org/contactOption"] = $contact['contactOption'];

                if (isset($contact['contactOption']))
                    $item["http://schema.org/contactOption"] = $contact['contactOption'];

                if (isset($contact['areaServed']))
                    $item["http://schema.org/areaServed"] = $contact['areaServed'];

                if (isset($contact['availableLanguage']))
                    $item["http://schema.org/availableLanguage"] = $contact['availableLanguage'];


                $contactPointList[] = (object)$item;

            }

            $doc["http://schema.org/contactPoint"] = $contactPointList;
        }


        $doc["@type"] = "http://schema.org/Organization";
        if (isset($options['name']))
            $doc["http://schema.org/name"] = $options['name'];
        if (isset($options['email']))
            $doc["http://schema.org/email"] = $options['email'];
        if (isset($options['phone']))
            $doc["http://schema.org/telephone"] = $options['phone'];


        self::add((object)$doc);
    }

    /**
     * Compacts JSON-LD document, encodes and adds to the application view `jsonld` parameter,
     * so it can later be registered using JsonLDHelper::registerScripts().
     * @param array|object $doc The JSON-LD document
     * @param array|null|object|string $context optional context. If not specified, schema.org vocabulary will be used.
     */
    public static function add($doc, $context = null)
    {
        if (is_null($context)) {
            // Using a simple context from the following comment would end up replacing `@type` keyword with `type` alias,
            // which is not recognized by Google's SDTT. So using a workaround instead
            // http://stackoverflow.com/questions/35879351/google-structured-data-testing-tool-fails-to-validate-type-as-an-alias-of-type
            //$context = (object)["@context" => "http://schema.org"];

            $context = (object)[
                "@context" => (object)["@vocab" => "http://schema.org/"]
            ];
        }

        $compacted = JsonLD::compact((object)$doc, $context);

        // We need to register it with "application/ld+json" script type, which is not possible with registerJs(),
        // so passing to layout where it can be registered via JsonLDHelper::registerScripts() using Html::script
        $view = Yii::$app->getView();
        $view->params['jsonld'][] = json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Registers JSON-LD scripts stored in the application view `jsonld` parameter.
     * This should be invoked in the <head> section of your layout.
     */
    public static function registerScripts()
    {
        $view = Yii::$app->getView();

        if (isset($view->params['jsonld'])) {
            foreach ($view->params['jsonld'] as $jsonld) {
                echo Html::script($jsonld, ['type' => 'application/ld+json']);
            }
        }
    }
}
