/**
 * @typedef {object} RequestPromise
 *
 * @description Обёртка для ароматизатора в ответе на запрос.
 *
 * @property {number} status  Статус загрузки: 0 - завершено, 1 - в процессе, -1 - ошибка
 * @property {string} message Сообщение для пользователя (в случае ошибки)
 */
class RequestPromise {
	constructor(status, message = '') {
		this.status = status;
		this.message = message;
	}
}

export default RequestPromise;