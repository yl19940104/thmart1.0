var app = new Vue({ 
	el: "#app",
	data:{
		supplierList: [],
	},
	mounted(){
		this.getData();
		setTimeout(function(){
			$("body .select2").select2();
		}, 500)
	},
	methods: {
		//如果已经提交过信息，则获取所有提交过的信息值
		addSupplierList() {
			var data = {id:''};
			this.supplierList.push(data);
			setTimeout(function(){
				$("body .select2").select2();
			}, 1)
		},
		addSupplier(id) {
			console.log(id);

		},
		getData() {
			var $id = $('#staffId').val();
			if ($id) {
				var param = {
					staff_id:$id,
					notShowLay:1
				}
				var that = this;
				th.request("POST", "Staff/Supplier/list", param, function(data){
					that.supplierList = data.data;
				});
			}
		},
		submitData() {
			var $id = $('#staffId').val();
			var $username = $('#username').val();
			var $password = $('#password').val();
			var arrElement = $(".checkbox input[type=checkbox]:checked");
			var arrId = [];
			for (var i = 0; i < arrElement.length; i++) {
				arrId.push($(arrElement[i]).val());
			}
			var param = {
				id:$id,
				username : $username,
				roleArray : arrId,
				password : $password,
				supplierList : this.supplierList,
			}
			th.request("POST", "Staff/edit", param, function(data){
				setTimeout(function(){
					window.location.href = document.referrer;
				},1000);
			});
		},
		deleteSupplierList(index) {
			this.supplierList.splice(index, 1);
		}
	}
})




