<?php
/**
 * InlineResponse2003DataCards
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * API продавца
 *
 * # Общее описание <style> .version {   border: 0.1rem #b3b3b3 solid ;   background-color: #F9F9F9;   color: #32329FE6;   height: 25px;   width: 150px;   text-align: center } </style> Wildberries API предоставляет продавцам возможность управления магазином и получения оперативной и статистической информации по протоколу HTTP RestAPI. <br> Описание API предоставляется в формате [Swagger](https://swagger.io/) (Open API) и может быть использовано для импорта в другие инструменты (такие как PostMan) или генерации клиентского кода на различных языках программирования с помощью [Swagger CodeGen](https://swagger.io/tools/swagger-codegen/)  <ul> <li> Описание в оригинальном swagger-формате <a href=\"/swagger\">swagger</a> <li> OpenAPI-файл <a href=\"/swagger.yaml\">swagger.yaml</a> </ul>  <br> Для ручной проверки API вы можете использовать: <ul> <li> Под ОС Windows - [PostMan](https://www.postman.com/) <li> Под ОС Linux - [curl](https://curl.se/)  </ul>  ## Поддержка <br> Техническая поддержка осуществляется через обращения в личном кабинете продавца. При создании нового обращения в техподдержку используйте категорию API. <br> Новости и изменения, касающиеся API, публикуются в [новостной ленте Wildberries](https://seller.wildberries.ru/news). <br> Также готовятся к публикации Release Notes по API на сайте.  После их выхода будет сделан соответствующий анонс.   ## Авторизация Вызов любого метода API должен быть авторизован.  Авторизация осуществляется по ключам API, которые  владелец личного кабинета (главный пользователь) самостоятельно  генерирует в разделе   [Профиль --> Настройки --> Доступ к API](https://seller.wildberries.ru/supplier-settings/access-to-api) для статистики   и [Профиль --> Настройки --> Доступ к новому API](https://seller.wildberries.ru/supplier-settings/access-to-new-api) для остальных методов.   Ключ должен передаваться в каждом HTTP-запросе.   ### Авторизация для методов Статистики При работе с методами Статистики ключ авторизации генерируется в разделе \"Профиль --> Настройки --> Доступ к API\". <br>Созданный ключ отображается в личном кабинете как \"Ключ для работы с API статистики x64\". <br>Его следует скопировать и добавлять в каждый запрос, прибавляя к запросу параметр `key`. <br>Выглядеть запрос будет примерно так: `https://suppliers-stats.wildberries.ru/api/v1/supplier/stocks?dateFrom=2022-03-25&key=xxxxxxxxxx`    ### Авторизация для нестатистических методов При работе со всеми методами кроме статистики ключ авторизации генерируется в разделе \"Профиль --> Настройки --> Доступ к новому API\". <br>Обратите внимание, что ключ отображается ТОЛЬКО в момент создания. Его надо сохранить, потому что больше его отобразить будет нельзя. <br>Созданный ключ следует добавлять в каждый запрос, прибавляя к запросу заголовок (http-header) формата `Authorization: xxxxxxxxxx`.  ## Форматы ### Дата и время Во всех методах API статистики дата и время передаются в формате [RFC3339](https://datatracker.ietf.org/doc/html/rfc3339).  <br> В большинстве случаев вы можете передать дату или дату со временем. Если время не указано, оно принимается равным 00:00:00. Время можно указывать с точностью до секунд или миллисекунд.  Литера `Z` в конце строки означает часовой пояс UTC. При ее отсутствии время считается в часовом поясе МСК (UTC+3). <br> Примеры: <ul> <li> `2019-06-20` <li> `2019-06-20T00:00:00Z` <li> `2019-06-20T23:59:59` <li> `2019-06-20T00:00:00.12345Z` <li> `2019-06-20T00:00:00.12345` <li> `2017-03-25T00:00:00` </ul>   ## Release Notes  #### 2022.09.20 v1.2  В связи с переходом на новое API Контента старые методы будут отключены. К их числу относятся: <ul> <li> `/card/_*` <li> `/api/v1/config/_*` <li> `/api/v1/directory/_*` </ul> Данные методы теперь возвращают код 404.  Новое API Контента описано в данном документе в разделах Контент / *
 *
 * OpenAPI spec version: 1.3
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.9
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * InlineResponse2003DataCards Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InlineResponse2003DataCards implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'inline_response_200_3_data_cards';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'sizes' => '\Swagger\Client\Model\InlineResponse2003DataSizes[]',
'media_files' => 'string[]',
'colors' => 'string[]',
'update_at' => 'string',
'vendor_code' => 'string',
'brand' => 'string',
'object' => 'string',
'nm_id' => 'int'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'sizes' => null,
'media_files' => null,
'colors' => null,
'update_at' => null,
'vendor_code' => null,
'brand' => null,
'object' => null,
'nm_id' => null    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'sizes' => 'sizes',
'media_files' => 'mediaFiles',
'colors' => 'colors',
'update_at' => 'updateAt',
'vendor_code' => 'vendorCode',
'brand' => 'brand',
'object' => 'object',
'nm_id' => 'nmID'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'sizes' => 'setSizes',
'media_files' => 'setMediaFiles',
'colors' => 'setColors',
'update_at' => 'setUpdateAt',
'vendor_code' => 'setVendorCode',
'brand' => 'setBrand',
'object' => 'setObject',
'nm_id' => 'setNmId'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'sizes' => 'getSizes',
'media_files' => 'getMediaFiles',
'colors' => 'getColors',
'update_at' => 'getUpdateAt',
'vendor_code' => 'getVendorCode',
'brand' => 'getBrand',
'object' => 'getObject',
'nm_id' => 'getNmId'    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['sizes'] = isset($data['sizes']) ? $data['sizes'] : null;
        $this->container['media_files'] = isset($data['media_files']) ? $data['media_files'] : null;
        $this->container['colors'] = isset($data['colors']) ? $data['colors'] : null;
        $this->container['update_at'] = isset($data['update_at']) ? $data['update_at'] : null;
        $this->container['vendor_code'] = isset($data['vendor_code']) ? $data['vendor_code'] : null;
        $this->container['brand'] = isset($data['brand']) ? $data['brand'] : null;
        $this->container['object'] = isset($data['object']) ? $data['object'] : null;
        $this->container['nm_id'] = isset($data['nm_id']) ? $data['nm_id'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets sizes
     *
     * @return \Swagger\Client\Model\InlineResponse2003DataSizes[]
     */
    public function getSizes()
    {
        return $this->container['sizes'];
    }

    /**
     * Sets sizes
     *
     * @param \Swagger\Client\Model\InlineResponse2003DataSizes[] $sizes Массив размеров для номенклатуры (для безразмерного товара все равно нужно передавать данный массив с одним элементом и  пустым Рос. размером, но с ценой и баркодом)
     *
     * @return $this
     */
    public function setSizes($sizes)
    {
        $this->container['sizes'] = $sizes;

        return $this;
    }

    /**
     * Gets media_files
     *
     * @return string[]
     */
    public function getMediaFiles()
    {
        return $this->container['media_files'];
    }

    /**
     * Sets media_files
     *
     * @param string[] $media_files Медиафайлы номенклатуры
     *
     * @return $this
     */
    public function setMediaFiles($media_files)
    {
        $this->container['media_files'] = $media_files;

        return $this;
    }

    /**
     * Gets colors
     *
     * @return string[]
     */
    public function getColors()
    {
        return $this->container['colors'];
    }

    /**
     * Sets colors
     *
     * @param string[] $colors Цвета номенклатуры
     *
     * @return $this
     */
    public function setColors($colors)
    {
        $this->container['colors'] = $colors;

        return $this;
    }

    /**
     * Gets update_at
     *
     * @return string
     */
    public function getUpdateAt()
    {
        return $this->container['update_at'];
    }

    /**
     * Sets update_at
     *
     * @param string $update_at Дата обновления
     *
     * @return $this
     */
    public function setUpdateAt($update_at)
    {
        $this->container['update_at'] = $update_at;

        return $this;
    }

    /**
     * Gets vendor_code
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->container['vendor_code'];
    }

    /**
     * Sets vendor_code
     *
     * @param string $vendor_code Текстовый идентификатор НМ поставщика
     *
     * @return $this
     */
    public function setVendorCode($vendor_code)
    {
        $this->container['vendor_code'] = $vendor_code;

        return $this;
    }

    /**
     * Gets brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->container['brand'];
    }

    /**
     * Sets brand
     *
     * @param string $brand Брэнд
     *
     * @return $this
     */
    public function setBrand($brand)
    {
        $this->container['brand'] = $brand;

        return $this;
    }

    /**
     * Gets object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->container['object'];
    }

    /**
     * Sets object
     *
     * @param string $object Категория для который создавалось КТ с данной НМ
     *
     * @return $this
     */
    public function setObject($object)
    {
        $this->container['object'] = $object;

        return $this;
    }

    /**
     * Gets nm_id
     *
     * @return int
     */
    public function getNmId()
    {
        return $this->container['nm_id'];
    }

    /**
     * Sets nm_id
     *
     * @param int $nm_id Числовой идентификатор номенклатуры Wildberries
     *
     * @return $this
     */
    public function setNmId($nm_id)
    {
        $this->container['nm_id'] = $nm_id;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}