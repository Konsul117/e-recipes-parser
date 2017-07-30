import EventEmitter from "events";

class AppEventEmitter extends EventEmitter {
	constructor() {
		super();

		this.EVENT_LOAD = 'load';
	}

	addLoadListener(callback) {
		this.on(this.EVENT_LOAD, callback);
	}

	emitLoad() {
		this.emit(this.EVENT_LOAD);
	}

	removeLoadListener(callback) {
		this.removeListener(this.EVENT_LOAD, callback);
	}
}

export default AppEventEmitter;