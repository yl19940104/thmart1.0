var app = new Vue({ 
	el: "#app",
	data:{
		title: '',
		pic: '',
		description: '',
		itemList: [],
		itemIdList: [],
		buttonShow: true,
		getParam : [],
	},
	mounted(){
		this.getItemList();
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
	            this.getParam.push(pair[1]);
	        }
			var id = pair[1];
			if (id) {
				var param = {
					id : id,
					notShowLay : true
				}
				var that = this;
				th.request("POST", "Article/adminArticleDetail", param, function(data) {
					console.log(data)
					if (data.data) {
						that.buttonShow = false;
						that.title = data.data.title;
						that.pic = data.data.pic;
						$('#picUrl').attr('src', that.pic);
						that.description = data.data.description;
						CKEDITOR.instances.detail.setData(data.data.article_content);
						that.itemIdList = data.data.itemList;
					}
				},function(err){
					console.log(err)
				});
			}
		},
		selectPic() {
			var that = this;
			CKFinder.popup({
		        chooseFiles: true,
		        width: 800,
		        height: 600,
		        onInit: function( finder ) {
		          finder.on( 'files:choose', function( evt ) {
		            var file = evt.data.files.first();
		            var output = document.getElementById('picUrl');
		            output.src = file.getUrl();
		            var param = window.location.origin;
					str = output.src;
					str = str.replace(param, '');
		            that.pic = str;
		          });
		          finder.on( 'file:choose:resizedImage', function( evt ) {
		            var output = document.getElementById('picUrl');
		            output.src = evt.data.resizedUrl;
		          });
		        }
		    })
		},
		getItemList() {
			var param = {
				notShowLay:true
			}
			var that = this;
			th.request("POST", "Item/idItemList", param, function(data) {
				that.itemList = data.data;
			},function(err){
				console.log(err)
			});
		},
		addItemList() {
			var data = {id:''}
			this.itemIdList.push(data);
		},
		deleteItemList(index) {
			this.itemIdList.splice(index, 1);
		},
		submitData() {
			var idArray = [];
			for (var i = 0; i < this.itemIdList.length; i++) {
				idArray.push(this.itemIdList[i].id)
			}
			var param = {
				title : this.title,
				pic : this.pic,
				description : this.description,
				itemIdList : idArray,
			}
			param.article_content = CKEDITOR.instances.detail.getData();
			param.id = this.getParam[0];
			th.request("POST", "Article/edit", param, function(data) {
				setTimeout(function(){
					window.location.href = document.referrer;
				},1000);
			},function(err){
				console.log(err)
			});
		}
	}
})


