<?php
/**
 * InlineResponse20028Sticker
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
 * # Общее описание <style> .version {   border: 0.1rem #b3b3b3 solid;   background-color: #F9F9F9;   color: #32329FE6;   height: 25px;   width: 150px;   text-align: center }, </style> <style> .warning {   border: 1.6rem #b3b3b3 solid;   background-color: #F9F9F9;   color: #247706;   text-align: center } </style>  Wildberries API предоставляет продавцам возможность управления магазином и получения оперативной и статистической информации по протоколу HTTP RestAPI. <br> Описание API предоставляется в формате [Swagger](https://swagger.io/) (Open API) и может быть использовано для импорта в другие инструменты (такие как PostMan) или генерации клиентского кода на различных языках программирования с помощью [Swagger CodeGen](https://swagger.io/tools/swagger-codegen/)  <ul> <li> Описание в оригинальном swagger-формате <a href=\"/swagger\">swagger</a> <li> OpenAPI-файл <a href=\"/swagger.yaml\">swagger.yaml</a> </ul>  <br> Для ручной проверки API вы можете использовать: <ul> <li> Под ОС Windows - [PostMan](https://www.postman.com/) <li> Под ОС Linux - [curl](https://curl.se/)  </ul> <br>  ## Поддержка Техническая поддержка осуществляется через диалоги в личном кабинете продавца. При создании нового обращения в техподдержку используйте категорию API. <br> Новости и изменения, касающиеся API, публикуются в [новостной ленте Wildberries](https://seller.wildberries.ru/news). <br> Также готовятся к публикации Release Notes по API на сайте.  После их выхода будет сделан соответствующий анонс. <br> <br> <br>  ## Авторизация Авторизация осуществляется по токенам API, которые  владелец личного кабинета (главный пользователь) самостоятельно  генерирует в разделе   [Профиль --> Настройки --> Доступ к новому API](https://seller.wildberries.ru/supplier-settings/access-to-new-api). <br>Обратите внимание, что токен отображается ТОЛЬКО в момент создания. Его надо сохранить, потому что больше его отобразить будет нельзя. <br>Созданный токен следует добавлять в каждый запрос, прибавляя к запросу заголовок (http-header) формата `Authorization: .........`. <br> <br>Внимание! Изменился домен для методов статистики, актуальный: https://statistics-api.wildberries.ru <br> <br> <br> ## Форматы ### Дата и время Во всех методах API статистики дата и время передаются в формате [RFC3339](https://datatracker.ietf.org/doc/html/rfc3339).  <br> В большинстве случаев вы можете передать дату или дату со временем. Если время не указано, оно принимается равным 00:00:00. Время можно указывать с точностью до секунд или миллисекунд.  Литера `Z` в конце строки означает часовой пояс UTC. При ее отсутствии время считается в часовом поясе МСК (UTC+3). <br> Примеры: <ul> <li> `2019-06-20` <li> `2019-06-20T00:00:00Z` <li> `2019-06-20T23:59:59` <li> `2019-06-20T00:00:00.12345Z` <li> `2019-06-20T00:00:00.12345` <li> `2017-03-25T00:00:00` </ul> <br> ## Release Notes    #### 2023.01.13 v1.7      Добавлено описание API рекламы  #### 2022.12.21 v1.6 Добавлена инструкция по загрузке статистики в Excel #### 2022.12.15 v1.5  Новая авторизация для методов API статистики 2022.12.15 в v1.5: #### 2022.10.31 v1.4  Метод будет отключен 2022.10.31 в v1.4: <ul> <li> `/content/v1/cards/list` </ul>  #### 2022.09.20 v1.2  В связи с переходом на новое API Контента старые методы будут отключены. К их числу относятся: <ul> <li> `/card/_*` <li> `/api/v1/config/_*` <li> `/api/v1/directory/_*` </ul> Данные методы теперь возвращают код 404.  Новое API Контента описано в данном документе в разделах Контент / * <br> <br>
 *
 * OpenAPI spec version: 1.7
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.40
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
 * InlineResponse20028Sticker Class Doc Comment
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InlineResponse20028Sticker implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'inline_response_200_28_sticker';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'wb_sticker_id' => 'int',
'wb_sticker_id_parts' => '\Swagger\Client\Model\InlineResponse20028StickerWbStickerIdParts',
'wb_sticker_encoded' => 'string',
'wb_sticker_svg_base64' => 'string',
'wb_sticker_zpl_v' => 'string',
'wb_sticker_zpl' => 'string'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'wb_sticker_id' => null,
'wb_sticker_id_parts' => null,
'wb_sticker_encoded' => null,
'wb_sticker_svg_base64' => null,
'wb_sticker_zpl_v' => null,
'wb_sticker_zpl' => null    ];

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
        'wb_sticker_id' => 'wbStickerId',
'wb_sticker_id_parts' => 'wbStickerIdParts',
'wb_sticker_encoded' => 'wbStickerEncoded',
'wb_sticker_svg_base64' => 'wbStickerSvgBase64',
'wb_sticker_zpl_v' => 'wbStickerZplV',
'wb_sticker_zpl' => 'wbStickerZpl'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'wb_sticker_id' => 'setWbStickerId',
'wb_sticker_id_parts' => 'setWbStickerIdParts',
'wb_sticker_encoded' => 'setWbStickerEncoded',
'wb_sticker_svg_base64' => 'setWbStickerSvgBase64',
'wb_sticker_zpl_v' => 'setWbStickerZplV',
'wb_sticker_zpl' => 'setWbStickerZpl'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'wb_sticker_id' => 'getWbStickerId',
'wb_sticker_id_parts' => 'getWbStickerIdParts',
'wb_sticker_encoded' => 'getWbStickerEncoded',
'wb_sticker_svg_base64' => 'getWbStickerSvgBase64',
'wb_sticker_zpl_v' => 'getWbStickerZplV',
'wb_sticker_zpl' => 'getWbStickerZpl'    ];

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
        $this->container['wb_sticker_id'] = isset($data['wb_sticker_id']) ? $data['wb_sticker_id'] : null;
        $this->container['wb_sticker_id_parts'] = isset($data['wb_sticker_id_parts']) ? $data['wb_sticker_id_parts'] : null;
        $this->container['wb_sticker_encoded'] = isset($data['wb_sticker_encoded']) ? $data['wb_sticker_encoded'] : null;
        $this->container['wb_sticker_svg_base64'] = isset($data['wb_sticker_svg_base64']) ? $data['wb_sticker_svg_base64'] : null;
        $this->container['wb_sticker_zpl_v'] = isset($data['wb_sticker_zpl_v']) ? $data['wb_sticker_zpl_v'] : null;
        $this->container['wb_sticker_zpl'] = isset($data['wb_sticker_zpl']) ? $data['wb_sticker_zpl'] : null;
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
     * Gets wb_sticker_id
     *
     * @return int
     */
    public function getWbStickerId()
    {
        return $this->container['wb_sticker_id'];
    }

    /**
     * Sets wb_sticker_id
     *
     * @param int $wb_sticker_id Идентификатор этикетки.
     *
     * @return $this
     */
    public function setWbStickerId($wb_sticker_id)
    {
        $this->container['wb_sticker_id'] = $wb_sticker_id;

        return $this;
    }

    /**
     * Gets wb_sticker_id_parts
     *
     * @return \Swagger\Client\Model\InlineResponse20028StickerWbStickerIdParts
     */
    public function getWbStickerIdParts()
    {
        return $this->container['wb_sticker_id_parts'];
    }

    /**
     * Sets wb_sticker_id_parts
     *
     * @param \Swagger\Client\Model\InlineResponse20028StickerWbStickerIdParts $wb_sticker_id_parts wb_sticker_id_parts
     *
     * @return $this
     */
    public function setWbStickerIdParts($wb_sticker_id_parts)
    {
        $this->container['wb_sticker_id_parts'] = $wb_sticker_id_parts;

        return $this;
    }

    /**
     * Gets wb_sticker_encoded
     *
     * @return string
     */
    public function getWbStickerEncoded()
    {
        return $this->container['wb_sticker_encoded'];
    }

    /**
     * Sets wb_sticker_encoded
     *
     * @param string $wb_sticker_encoded Закодированное значение стикера (представляется на этикетке в формате Code-128).
     *
     * @return $this
     */
    public function setWbStickerEncoded($wb_sticker_encoded)
    {
        $this->container['wb_sticker_encoded'] = $wb_sticker_encoded;

        return $this;
    }

    /**
     * Gets wb_sticker_svg_base64
     *
     * @return string
     */
    public function getWbStickerSvgBase64()
    {
        return $this->container['wb_sticker_svg_base64'];
    }

    /**
     * Sets wb_sticker_svg_base64
     *
     * @param string $wb_sticker_svg_base64 Полное представление этикетки в векторном формате.
     *
     * @return $this
     */
    public function setWbStickerSvgBase64($wb_sticker_svg_base64)
    {
        $this->container['wb_sticker_svg_base64'] = $wb_sticker_svg_base64;

        return $this;
    }

    /**
     * Gets wb_sticker_zpl_v
     *
     * @return string
     */
    public function getWbStickerZplV()
    {
        return $this->container['wb_sticker_zpl_v'];
    }

    /**
     * Sets wb_sticker_zpl_v
     *
     * @param string $wb_sticker_zpl_v Полное представление этикетки в формате ZPL (вертикальное положение).
     *
     * @return $this
     */
    public function setWbStickerZplV($wb_sticker_zpl_v)
    {
        $this->container['wb_sticker_zpl_v'] = $wb_sticker_zpl_v;

        return $this;
    }

    /**
     * Gets wb_sticker_zpl
     *
     * @return string
     */
    public function getWbStickerZpl()
    {
        return $this->container['wb_sticker_zpl'];
    }

    /**
     * Sets wb_sticker_zpl
     *
     * @param string $wb_sticker_zpl Полное представление этикетки в формате ZPL (горизонтальное положение)
     *
     * @return $this
     */
    public function setWbStickerZpl($wb_sticker_zpl)
    {
        $this->container['wb_sticker_zpl'] = $wb_sticker_zpl;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    #[\ReturnTypeWillChange] 
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
    #[\ReturnTypeWillChange] 
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
    #[\ReturnTypeWillChange] 
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
    #[\ReturnTypeWillChange] 
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