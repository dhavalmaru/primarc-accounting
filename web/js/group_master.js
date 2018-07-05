var data = [{
	id:1,name:"Front-end Development",pid:0,innercode:"1A"
},
{
	id:2,name:"JavaScript",pid:1,innercode:"1A2A"
},
{
	id:6,name:"HTML5",pid:1,innercode:"1A3A"
},
{
	id:4,name:"ReactJS",pid:2,innercode:"1A4A"
},
{
	id:5,name:"React Native",pid:4,innercode:"1A5A"
},
{
	id:3,name:"PHP",pid:0,innercode:"1A6A"
}];

$("#bs-treeetable").bstreetable({
	data:data,
	maintitle:"My skills",
	nodeaddCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback 新增的时候会返回一个字段叫pinnercode,表示父节点的innercode
		callback({id:18,name:data.name,innercode:"ttttt",pid:data.pid});
	},
	noderemoveCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback
		callback();
	},
	nodeupdateCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback
		callback();
	}
}
);

$("#bs-ml-treetable").bstreetable({
	data:data,
	maintitle:"公司名称",
	nodeaddCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback innercode
		callback({id:18,name:data.name,innercode:"ttttt",pid:data.pid});
	},
	noderemoveCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback
		callback();
	},
	nodeupdateCallback:function(data,callback){
		alert(JSON.stringify(data));
		//do your things then callback
		callback();
	},
	extfield:[
		{title:"innercode",key:"innercode",type:"input"}
	]//{title:"列名",key:"",type:"input"} input表示是输入框
})