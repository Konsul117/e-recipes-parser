class FlavorRequest {
	constructor(nameQuery, brandsIds = [], sourcesIds = [], limit = 20) {
		this.nameQuery  = nameQuery;
		this.brandsIds  = brandsIds;
		this.sourcesIds = sourcesIds;
		this.limit      = limit;
	}
}

export default FlavorRequest;