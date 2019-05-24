var app = new Vue({ 
	el: "#app",
	data:{
		supplier_name: '',
		contacts_name: '',
		contacts_phone: '',
		contacts_email: '',
		contacts_address: '',
		param: '',
		number: '',
		sale: '',
		remark: '',
		catOneList: '',
		catTwoList: [],
		pointList: [],
		is_effective: '',
		buttonShow: true,
		supplierId: ''
	},
	mounted(){
		this.getCatOneList();
		this.getAllData();
	},
	methods: {
		//如果已经提交过信息，则获取所有提交过的信息值
		getAllData() {
			var query = window.location.search.substring(1);
	       	var vars = query.split("&");
	       	for (var i=0;i<vars.length;i++) {
	            var pair = vars[i].split("=");
	            /*if (pair[0] == 'id') {
	            }*/
	        }
			var id = pair[1] ? pair[1] : $('#supplierId').val();
			this.supplierId = id;
			if (id) {
				var param = {
					id : id,
					notShowLay : true
				}
				var that = this;
				th.request("POST", "Supplier/detail", param, function(data) {
					if (data.data[0]) {
						that.supplier_name = data.data[0].supplier_name;
						that.contacts_name = data.data[0].contacts_name;
						that.contacts_phone = data.data[0].contacts_phone;
						that.contacts_email = data.data[0].contacts_email;
						that.contacts_address = data.data[0].contacts_address;
						that.param = data.data[0].param;
						that.number = data.data[0].number;
						that.sale = data.data[0].sale;
						that.remark = data.data[0].remark;
						that.is_effective = data.data[0].is_effective;
						data.data[0].pointList.forEach(function(value,index,array){
							var data = {
								catOneId : '',
								catTwoId : '',
								point: ''
							}
							that.pointList.push(data);
							that.pointList[index].point = value.point*0.01;
						  	that.pointList[index].catOneId = value.catOneId;
						  	that.pointList[index].catTwoId = value.catTwoId;
						  	var param2 = {
								fname: value.catOneId,
								notShowLay:true
							}
							th.request("POST", "Category/list", param2, function(data) {
								that.catTwoList.splice(index, 0, data.data);
							},function(err){
								console.log(err)
							});
					　　});
					}
				},function(err){
					console.log(err)
				});
			}
		},
		getCatOneList() {
			var param = {
				fname: 0,
				notShowLay:true
			}
			var that = this;
			th.request("POST", "Category/list", param, function(data) {
				data.data.splice(0,1);
				that.catOneList = data.data;
			},function(err){
				console.log(err)
			});
		},
		addPointList() {
			var data = {
				catOneId : '',
				catTwoId : '',
				point: ''
			}
			this.pointList.push(data);
		},
		deletePointList(index) {
			this.pointList.splice(index, 1);
			this.catTwoList.splice(index, 1);
		},
		addPointListValue(index, name, e) {
			this.pointList[index][name] = e.target.value;
			if (name == 'catOneId') {
				if (e.target.value) {
					var param = {
						fname: e.target.value,
						notShowLay:true
					}
					var catTwoList = this.catTwoList;
					th.request("POST", "Category/list", param, function(data) {
						catTwoList.splice(index, 1, data.data);
					},function(err){
						console.log(err)
					});
					this.catTwoList = catTwoList;
				}
			}
		},
		getCatTwoId(index,index2) {
			return this.catTwoList[index][index2].id;
		},
		getCatOneId(index) {
			return this.catOneList[index].id;
		},
		submitData() {
			var param = {
				supplier_name : this.supplier_name,
				contacts_name : this.contacts_name,
				contacts_phone : this.contacts_phone,
				contacts_email : this.contacts_email,
				contacts_address : this.contacts_address,
				param : this.param,
				number : this.number,
			    sale : this.sale,
				remark : this.remark,
				pointList : this.pointList,
			}
			if (this.supplierId) {
				param.id = this.supplierId;
			}
			th.request("POST", "Supplier/edit", param, function(data) {
				setTimeout(function(){
					window.location.href = document.referrer;
				},1000);
			},function(err){
				console.log(err)
			});
		},
		submitEffective() {
			var param = {
				id : this.supplierId
			}
			th.request("POST", "Supplier/effective", param, function(data) {
				location.reload();
			},function(err){
				console.log(err)
			});	
		}
	}
})


