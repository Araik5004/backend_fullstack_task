var app = new Vue({
	el: '#app',
	data: {
		login: '',
		pass: '',
		post: false,
		invalidLogin: false,
		invalidCommentText: false,
		invalidPass: false,
		invalidSum: false,
		posts: [],
		addSum: 0,
		amount: 0,
		likes: 0,
		commentText: '',
		packs: [
			{
				id: 1,
				price: 5
			},
			{
				id: 2,
				price: 20
			},
			{
				id: 3,
				price: 50
			},
		],
	},
	computed: {
		test: function () {
			var data = [];
			return data;
		}
	},
	created(){
		var self = this
		axios
			.get('/main_page/get_all_posts')
			.then(function (response) {
				self.posts = response.data.posts;
			})
	},
	methods: {
		logout: function () {
			console.log ('logout');
		},
		logIn: function () {
			var self= this;
			if(self.login === ''){
				self.invalidLogin = true
			}
			else if(self.pass === ''){
				self.invalidLogin = false
				self.invalidPass = true
			}
			else{
				self.invalidLogin = false
				self.invalidPass = false
				const params = new URLSearchParams();
				params.append('login', self.login);
				params.append('password', self.pass);
				const headers = {
					'content-type': 'application/x-www-form-urlencoded;charset=utf-8'
				};
				axios.post("/main_page/login", params, { headers })
				/*axios.post('/main_page/login', {
					login: self.login,
					password: self.pass
				})*/
					.then(function (response) {
						if(response.data.status == 'success' )
						{
							window.location.reload()
						}
						setTimeout(function () {
							$('#loginModal').modal('hide');
						}, 500);
					})
			}
		},
		comment: function () {
			var self= this;
			if( self.commentText === '')
			{
				self.invalidCommentText = true
			}
			else{
				self.invalidCommentText = false;
				const params = new URLSearchParams();
				params.append('text', self.commentText);
				params.append('assign_id', self.post.id);
				const headers = {
					'content-type': 'application/x-www-form-urlencoded;charset=utf-8'
				};
				axios.post("/main_page/comment", params, { headers })
					.then(function (response) {
						if(response.data.status === 'success' )
						{
							setTimeout(function () {
								$('#postModal').modal('hide');
							}, 500);
						}
					})
			}
		},
		fiilIn: function () {
			var self= this;
			if(self.addSum === 0){
				self.invalidSum = true
			}
			else{
				self.invalidSum = false
				const params = new URLSearchParams();
				params.append('sum', self.addSum);
				const headers = {
					'content-type': 'application/x-www-form-urlencoded;charset=utf-8'
				};
				axios.post('/main_page/add_money', params, { headers })
				/*axios.post('/main_page/add_money', {
					sum: self.addSum,
				})*/
					.then(function (response) {
						if(response.data.status === "success")
						{
							setTimeout(function () {
								$('#addModal').modal('hide');
							}, 500);
						}
						else
						{
							self.invalidSum = true
						}
					})
			}
		},
		openPost: function (id) {
			var self= this;
			axios
				.get('/main_page/get_post/' + id)
				.then(function (response) {
					self.post = response.data.post;
					if(self.post){
						setTimeout(function () {
							$('#postModal').modal('show');
						}, 500);
					}
				})
		},
		addLike: function (id) {
			var self= this;
			axios
				.get('/main_page/like?post_id='+id)
				.then(function (response) {
					if(response.data.status === "success")
					{
						self.likes = response.data.likes;
					}
					else
					{
						//show message ,  no money no honey :)
					}
				})

		},
		buyPack: function (id) {
			var self= this;
			const params = new URLSearchParams();
			params.append('id', id);
			const headers = {
				'content-type': 'application/x-www-form-urlencoded;charset=utf-8'
			};
			axios.post('/main_page/buy_boosterpack', params, { headers })
			/*axios.post('/main_page/buy_boosterpack', {
				id: id,
			})*/
				.then(function (response) {
					if(response.data.status === "success")
					{
						self.amount = response.data.amount
						if(self.amount !== 0){
							setTimeout(function () {
								$('#amountModal').modal('show');
							}, 500);
						}
					}
					else
					{
						//something wrong
					}

				})
		}
	}
});

