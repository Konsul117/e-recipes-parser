/**
 * @typedef {object} Response
 *
 * @description Описание объекта ответа на запрос обновления чата
 *
 * @property {number} result 	Результат
 * @property {mixed}  data 	События чата
 * @property {string} message 	Сообщение (при ошибке)
 */

/**
 * @typedef {object} FlavorRequest
 *
 * @description Запрос получения ароматизаторов.
 *
 * @property {string} 			searchQuery Строка запроса поиска
 * @property {number|undefined} limit 		Лимит поиска
 */

/**
 * @typedef {object} FlavorsResponse
 *
 * @description Ответ на запрос получения ароматизаторов.
 *
 * @property {FlavorItemResponse[]} flavors    Ароматизаторы
 * @property {number} 				totalCount Общее количество найденных варианатов
 */

/**
 * @typedef {object} FlavorItemResponse
 *
 * @description Обёртка для ароматизатора в ответе на запрос.
 *
 * @property {number}   id         Идентификатор
 * @property {string}   name       Название
 * @property {number}   brandId    Идентификатор бренда
 * @property {number[]} sourcesIds Общее количество найденных варианатов
 */