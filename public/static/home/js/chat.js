/**
 * 聊天首页
 * @param {Object} window
 */
(function(window){
	
	var vm = new Vue({
		el:'#chat-room',
		data:{
			isInit:false,
			message_content:'',
			member:null,
			doctor:null,
			ws:null,
			messages:[],
			rightShow:false,
			is_online:false,
			is_client:false
			
		},
		computed:{
			showMessage:function(){
				if(!this.isInit){
					return "您正在接通刘医生请稍等...";
				}
				if(!this.is_online){
					return "对方已离线";
				}
				return "对方在线";
			},
			mhead:function(){
				return this.member.headimgurl?this.member.headimgurl:'/static/home/img/touxiang.jpg';
			},
			dhead:function(){
				return this.doctor.faceimgurl?this.doctor.faceimgurl:'/static/home/img/tx_ys.jpg';
			}
		},
		created:function(){
			this.getDataInfo();
		},
		methods:{
			getDataInfo:function(){
				var self = this;
				$.getJSON(urls.getDataUrl,function(res){
					if(res.errcode == 0){
						self.member = res.member;
						self.doctor = res.doctor;
						self.connect();
					}
				});
			},
			uploadImage:function(){
				
			},
			send:function(){
				if(!this.message_content){
					layer.msg("请填写发送的内容");
					return false;
				}
				if(this.doctor){
					var senddata = {
						type:'message',
						data:{
							self_id:this.member.id,
							to_uid:this.doctor.id,
							content:this.message_content
						}
					}
					this.ws.send(JSON.stringify(senddata));
					var newMessage = {self:1,message:this.message_content};
					this.messages.push(newMessage);
					this.message_content = '';
				}
			},
			
			connect:function(){
				// 创建websocket
				if(!this.ws){
					var ws = new WebSocket("ws://"+document.domain+":8282");
			       	ws.onopen = this.onopen;
			       	// 当有消息时根据消息类型显示不同信息
			       	ws.onmessage = this.onmessage; 
			       	ws.onclose = function() {
			          	console.log("连接关闭，定时重连");
			          	self.connect();
			       	};
			       	ws.onerror = function() {
			           	console.log("出现错误");
			       	};
			       	this.ws = ws;
				}
			},
			onopen:function(){
				console.log('onopen');
				var senddata = {
					type:'init',
					member_id:this.member.id,
					doctor_id:this.doctor.id
				};
				this.ws.send(JSON.stringify(senddata));
			},
			onmessage:function(e){
				// 所有的业务逻辑
				var self = this;
				var data = JSON.parse(e.data);
				console.log(data);
				switch(data.message_type){
					case 'init':
						// 返回是否可以发送消息 和 医生是否在线is_online,is_client
						if(data.status == 'error'){
							alert("身份认证失败");
							return false;
						}
						self.is_online = data.is_online;
						self.is_client = data.is_client;
						self.isInit = true;
						break;
					case 'update':
						self.is_online = data.is_online;
						self.is_client = data.is_client;
						break;
					case 'toupdate':
						self.ws.send(JSON.stringify({type:'update'}));
						break;
					case 'message':
						var messagedata = data.data;
						console.log(messagedata);
						var newMessage = {
							self:0,
							message:messagedata.content
						};
						self.messages.push(newMessage);
						break;
				}
			},
			showNewMessage:function(){
				console.log(this.$refs);
				var height= this.$refs.chatbox.getBoundingClientRect().height;
            	$(".chat-box").scrollTop(height); 
			}
		}
	});
	
	function loading(){
		
	}
})(window);
