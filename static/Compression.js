function Compression(threshold){
	this.data = [];
	this.check_list = [];
	this.temp_check_list = [];
	this.display_list = [];
	this.display_number = 0;
	this.threshold = threshold;
	this.result = [];
}

Compression.prototype.getDistance = function(p1,p2){
	return Math.abs(p1 - p2);
}
Compression.prototype.searchParentsAndChilren= function(){
	if(this.head.length==0)
		return false;

	startIndex = this.head.pop();
	endIndex = this.foot.pop();

	if((endIndex-startIndex)>2){
		
		// Search the index of the point with the maximal synchronic distance among the points, whose indexes are in [startIndex, ..., endIndex].
		var dT = this.data[endIndex][0] - this.data[startIndex][0];
		var vD = (this.data[endIndex][1] - this.data[startIndex][1])/parseFloat(dT);
		var maxError = 0.0;
		var maxIndex = -1;
		for(var i=(startIndex+1) ; i<=(endIndex-1);i++){
			var tempD = (this.data[i][0] - this.data[startIndex][0])*vD + this.data[startIndex][1];
			var distance = this.getDistance(this.data[i][1],tempD);
			if(distance >= maxError&& distance>=this.threshold){
				maxIndex = i;
				maxError = distance;
			}
		}

		if(maxIndex!=-1)
		{
			this.head.push(startIndex, maxIndex);
			this.foot.push(maxIndex, endIndex);
			this.result.push(maxIndex);
		}
	}
	return true;
	//this.searchParentsAndChilren();
	//this.searchParentsAndChilren(startIndex,maxIndex);
	//this.searchParentsAndChilren(maxIndex,endIndex);
};

Compression.prototype.compress = function(array){
	if(array.length<=2)
		return;

	for(var i=0; i < array.length;i++){
		this.data.push(array[i]);
		this.data[this.data.length-1][1] = parseInt(this.data[this.data.length-1][1]);
	}
	this.head = new Array();
	this.foot = new Array();
	this.head.push(0);
	this.foot.push(this.data.length-1);
	this.result.push(0,this.data.length-1);
	//this.searchParentsAndChilren(0,this.data.length-1);
	while(this.searchParentsAndChilren()){};
	this.result.sort(function(a,b){return a-b});
	return this.result;
};


