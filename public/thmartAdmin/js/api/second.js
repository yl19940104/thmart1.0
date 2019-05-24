var app = new Vue({
	el: '#app',
	data: {
		getParam : [],//url传参,getParam[0]为supplierId,getParam[1]为catOneId,getParam[2]为catTwoId,getParam[3]为itemId
		shopId : '',
		categoryFirst : '',
		categorySecond : '',
		categoryThird : '',
		categorySecondList : [],
		categoryThirdList : [],
		point : '',
		typed : '',
		selectBrand: '',
		titled: '',
		subTitle: '',
		enTitle: '',
		titleLink: '',
		pic: [],//没法直接赋值，只能定义为数组然后用push函数
		picList: [],
		propValue: [], //后台返回的属性列表
		propName: '', //自定义的属性名
		propList: [], //动态生成的属性数组
		index: 1,
		productSkuNameList: ['产品sku1'], //产品sku名字
		finalSkuListArr: [], //最終显示sku数组
		finalSkuListArrSubmit: [], //最終显示sku数组
		skuData: [],//修改商品信息时借口返回的sku信息
		showCategoryThree: false,//是否显示分类三下拉框，1代表显示，2代表不显示
	},
	mounted(){
		this.getData();
	},
	methods: {
		getData() {
			var that = this;
			var param = {
	    		notShowLay:true
	    	}
			th.request("POST", "Category/Prop/list", param, function(data){
		       that.propValue = data.data;
			})
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
				var pair = vars[i].split("=");
				/*if (pair[0] == 'id') {
                }*/
				this.getParam.push(pair[1]);
			}
			if (this.getParam[1] != 1) this.typed = 1;
			if (this.getParam[4]) that.showCategoryThree = true;
			var param = {
	        	supplierId : this.getParam[0],
	        	catOneId : this.getParam[1],
	        	catTwoId : this.getParam[2],
				catThreeId : this.getParam[4],
	        	notShowLay: true
	        }
	        th.request("POST", "Supplier/point", param, function(data){
		       that.point = data.data[0].point * 0.01;
			})
			this.shopId = this.getParam[0];
			this.categoryFirst = this.getParam[1];
			this.categorySecond = this.getParam[2];
			this.categoryThird = this.getParam[4];
			var param = {
				fname : this.categoryFirst,
				notShowLay : 1,
			}
			if (this.categoryFirst != 1) this.typed = 1;
			th.request("POST", "Category/list", param, function(data){
		    	that.categorySecondList = data.data;
			})
			console.log(this.categorySecond)
			if (this.categorySecond == 161) {
				var param = {
					fname : this.categorySecond,
					notShowLay : 1,
				}
				th.request("POST", "Category/list", param, function(data){
					that.categoryThirdList = data.data;
				})
			}
			var itemId = this.getParam[3];
			//如果获取到itemId,则表示上传商品
			if (itemId) {
				var param = {
		        	itemId : itemId,
		        	notShowLay: true
		        }
		        th.request("POST", "Item/itemEditDetail", param, function(data){
		        	console.log(data)
			    	that.typed = data.data.item.type;
			    	that.selectBrand = data.data.item.brandName;
			    	that.titled = data.data.item.title;
			    	that.subTitle = data.data.item.subTitle;
			    	that.enTitle = data.data.item.enTitle;
			    	that.titleLink = data.data.item.titleLink;
			    	that.pic[0] = data.data.item.pic;
			    	$('#pic').attr('src', that.pic[0]);
			    	for (var i = 0; i < data.data.item.picList.length;i++) {
			    		that.picList.push(data.data.item.picList[i].pic);
			    	}
			    	CKEDITOR.instances.detail.setData(data.data.item.detail);
			    	var skuLength = data.data.sku.length;
			    	that.index = skuLength;
			    	for (var j = 2; j <= skuLength; j++) {
			    		that.productSkuNameList.push('产品sku'+j);
			    	}
			    	that.propList = data.data.propList;
			    	that.skuData = data.data.sku;
				})
			}
		},
		submitData() {
			var that = this;
			var detail = CKEDITOR.instances.detail.getData();
			var param = {
				shopId : this.shopId,
	        	categoryName : this.categoryFirst,
	        	categoryTwoName : this.categorySecond,
				categoryThreeName : this.categoryThird,
	        	type : this.typed,
	        	title : this.titled,
	        	subTitle : this.subTitle,
	        	enTitle : this.enTitle,
	        	titleLink :this.titleLink,
	        	brandName :this.selectBrand,
	        	pic : this.pic[0],
	        	detail : this.detail, 
	        	point : this.point,
	        	detail : detail,
	        	picList : this.picList, 
	        	notShowLay: false,
	        	id: this.getParam[3]
			}
			th.request("POST", "Item/create", param, function(data){
			    var param2 = {
			    	'itemId' : data.data,
			    	'skus' : that.finalSkuListArrSubmit,
			    	'notShowLay' : false 
			    }
			    th.request("POST", "Sku/edit", param2, function(data){
			    	setTimeout(function(){
						window.location.href = document.referrer;
					},1000);
			    })
			});
		},
		//商品上下架
		changeAudited(param) {
			var data = {
				'id' : this.getParam[3],
				'audited' : param
			}
			th.request("POST", "Item/changeAudited", data, function(data){
		    	setTimeout(function(){
					window.location.href = document.referrer;
				},1000);
		    })
		},
		//添加类目属性
		addPropValue() {
			if (this.propName) {
				var value = {
					id: 170,
					name: this.propName
				} 
				this.propValue.push(value);
			} 
			this.propName = '';
		},
		//选择二级分类
		selectCategorySecond() {
			var param = {
				fname : this.categoryFirst,
				notShowLay : 1,
			}
			if (this.categoryFirst != 1) this.typed = 1;
			this.categorySecond = '';
			var that = this;
			th.request("POST", "Category/list", param, function(data){
				console.log(data.data)
		    	that.categorySecondList = data.data;
			})
		},
		// 选择属性,存入数组propList中
		checkProp(item) {
			var propItem = {
				id: item.id,
				name: item.name,
				arr: []
			}
			if (!this.inArray(propItem)) {
				this.propList.push(propItem);
			}
			
		},
		//判断某一元素是否存在于propList
		inArray(propItem) {
			var buer = false;
			for (var i = 0; i < this.propList.length; i++) {
				if (this.propList[i].name == propItem.name) {
					buer = true;
				}
			}
			return buer;
		},
		//点击第一个动态表格的+号，横向增加"产品skuID"
		addProductSkuName() {
			this.index++;
			this.productSkuNameList.push('产品sku'+ this.index);
		},
		//点击产品skuId旁的减号
		deleteProductSkuName(index) {
			this.productSkuNameList.splice(index, 1);
			for (var i = 0; i < this.propList.length; i++) {
				this.propList[i].arr.splice(index, 1);
			}
		},
		//失去光标，往propList对应的arr中增加属性值
		addPropItem(rowindex,index,e) {
			this.propList[rowindex].arr.splice(index, 1, e.target.value);
		},
		//点击第一个动态表格的-号，删除propList中对应的数组元素
		deletePropValue(rowindex) {
			this.propList.splice(rowindex, 1);
		},
		//添加元素到finalSkuListArrSubmit
		addFinalSkuListArr(index, type, e) {
			//vue无法实时监控到finalSkuListArrSubmit的变化，但是最后提交时是可以获取到的
			this.finalSkuListArrSubmit[index][type] = e.target.value;
			if (type == 'price' && this.point > 0) {
				this.finalSkuListArrSubmit[index]['costPrice'] = parseInt(e.target.value * (1-this.point*0.01)*100)*0.01;
				this.finalSkuListArrSubmit[index]['costPrice'].toString();
			}
			this.finalSkuListArr[index][type] = e.target.value;
			if (type == 'price' && this.point > 0) {
				this.finalSkuListArr[index]['costPrice'] = parseInt(e.target.value * (1-this.point*0.01)*100)*0.01;
				this.finalSkuListArrSubmit[index]['costPrice'].toString();
			}
		},
		//添加sku图片到finalSkuListArr
		selectImage(index) {
			var finalSkuListArrSubmit = this.finalSkuListArrSubmit;
			CKFinder.popup({
		        chooseFiles: true,
		        width: 800,
		        height: 600,
		        onInit: function( finder ) {
		          finder.on( 'files:choose', function( evt ) {
		            var file = evt.data.files.first();
		            var output = document.getElementById('ckfinderImage'+index);
		            output.src = file.getUrl();
		            /*finalSkuListArrSubmit[index][image] = output.src;*/
		            var param = window.location.origin;
					str = output.src;
					str = str.replace(param, '');
		            finalSkuListArrSubmit[index].pic = str;
		          });
		          finder.on( 'file:choose:resizedImage', function( evt ) {
		            var output = document.getElementById('ckfinderImage'+index);
		            output.src = evt.data.resizedUrl;
		          });
		        }
		    })
		    this.finalSkuListArrSubmit = finalSkuListArrSubmit;
		},
		//sku图片dom动态获取id名
		getImageId(index) {
			return 'ckfinderImage'+index;
		},
		getPicUrl(item) {
			return item;
 		},
		//获取主图
		selectPic() {
			var pic = this.pic;
			CKFinder.popup({
		        chooseFiles: true,
		        width: 800,
		        height: 600,
		        onInit: function( finder ) {
		          finder.on( 'files:choose', function( evt ) {
		            var file = evt.data.files.first();
		            var output = document.getElementById('pic');
		            output.src = file.getUrl();
		            var param = window.location.origin;
					str = output.src;
					str = str.replace(param, '');
		            pic[0] = str;
		            var size = file.attributes.imageResizeData.attributes.originalSize;
		            console.log(size)
	              	if (size != '800x800') {
	              		output.src = '';
	              		pic[0] = '';
	              		alert('图片尺寸必须是800*800')
	              	}
		          });
		          finder.on( 'file:choose:resizedImage', function( evt ) {
		            var output = document.getElementById('pic');
		            output.src = evt.data.resizedUrl;
		          });
		        }
		    })
		    this.pic = pic;
		},
		//获取轮播图
		selectPicList(index) {
			var picList = this.picList;
			CKFinder.popup({
		        chooseFiles: true,
		        width: 800,
		        height: 600,
		        onInit: function( finder ) {
		           finder.on( 'files:choose', function( evt ) {
		            var file = evt.data.files.first();
		            var output = document.getElementById('picOne');
		            output.src = file.getUrl();
		            var param = window.location.origin;
					str = output.src;
					str = str.replace(param, '');
		            picList.push(str);
		          });
		          finder.on( 'file:choose:resizedImage', function( evt ) {
		            /*var output = document.getElementById('picOne');
		            output.src = evt.data.resizedUrl;*/
		          });
		        }
		    })
		    this.picList = picList;
		},
		deletePicList(index) {
			this.picList.splice(index,1); 
		}
	},
	watch: {
		propList: {
			handler: function(newValue, oldvalue) {
				this.finalSkuListArr = [];
				var arr = [];
				if (newValue.length > 0) {
					var propListArrLength = newValue[0].arr.length;
					for (var j = 0; j < propListArrLength; j++) {
						for (var i = 0; i < newValue.length; i++) {
							if (!(arr[j] instanceof Array)) arr[j] = new Array();
							arr[j][i] = newValue[i].arr[j];
						}
					}
					for (var j = 0; j < propListArrLength; j++) {
						var itemObj = {
							price: '',
							costPrice: '',
							stock: '',
							size: '',
							weight: '',
							place: '',
							type: '',
							pic: '',
							arr: arr[j]
						}
						this.finalSkuListArr.push(itemObj);
					}
				}
                var finalSkuListArrSubmit = this.finalSkuListArrSubmit;
                var itemObj = {
						price: '',
						costPrice: '',
						stock: '',
						size: '',
						weight: '',
						place: '',
						type: '',
						arr: [],
						pic: ''
					}
                finalSkuListArrSubmit.push(itemObj);
				this.finalSkuListArrSubmit = [];
				var arr = [];
				if (newValue.length > 0) {
					var propListArrLength = newValue[0].arr.length;
					for (var j = 0; j < propListArrLength; j++) {
						for (var i = 0; i < newValue.length; i++) {
							if (!(arr[j] instanceof Object)) arr[j] = new Object();
							arr[j][newValue[i].name] = newValue[i].arr[j];
						}
					}
					for (var j = 0; j < propListArrLength; j++) {
						var itemObj = {
							price: this.finalSkuListArr[j].price,
							costPrice: this.finalSkuListArr[j].costPrice,
							stock: this.finalSkuListArr[j].stock,
							size: this.finalSkuListArr[j].size,
							weight: this.finalSkuListArr[j].weight,
							place: this.finalSkuListArr[j].place,
							type: this.finalSkuListArr[j].type,
							arr: arr[j],
							pic: this.finalSkuListArr[j].pic
						}
						this.finalSkuListArrSubmit.push(itemObj);
					}
				}
				if (this.getParam[3]) {
					var skuAmount = this.skuData.length;				
					for (var i = 0; i < skuAmount; i++) {
						this.finalSkuListArrSubmit[i].price = this.skuData[i].price;
						this.finalSkuListArr[i].price = this.skuData[i].price;
						this.finalSkuListArrSubmit[i].costPrice = this.skuData[i].costPrice;
						this.finalSkuListArr[i].costPrice = this.skuData[i].costPrice;
						this.finalSkuListArrSubmit[i].pic = this.skuData[i].pic;
						this.finalSkuListArr[i].pic = this.skuData[i].pic;
						this.finalSkuListArrSubmit[i].place = this.skuData[i].place;
						this.finalSkuListArr[i].place = this.skuData[i].place;
						this.finalSkuListArrSubmit[i].size = this.skuData[i].size;
						this.finalSkuListArr[i].size = this.skuData[i].size;
						this.finalSkuListArrSubmit[i].stock = this.skuData[i].stock;
						this.finalSkuListArr[i].stock = this.skuData[i].stock;
						this.finalSkuListArrSubmit[i].type = this.skuData[i].type;
						this.finalSkuListArr[i].type = this.skuData[i].type;
						this.finalSkuListArrSubmit[i].weight = this.skuData[i].weight;
						this.finalSkuListArr[i].weight = this.skuData[i].weight;
						this.finalSkuListArrSubmit[i].id = this.skuData[i].id;
					}
				}
			},
			immediate: true,
			deep: true
		},
		categorySecond: {
			handler: function (newValue, oldvalue) {
				if (newValue != 161) {
					this.showCategoryThree = false;
					this.categoryThird = 0;
				} else {
					this.showCategoryThree = true;
				}
				var param = {
					fname : this.categorySecond,
					notShowLay : 1,
				}
				var that = this;
				th.request("POST", "Category/list", param, function(data){
					console.log(data.data)
					that.categoryThirdList = data.data;
				})
			},
			immediate: true,
			deep: true
		},
	}
})
