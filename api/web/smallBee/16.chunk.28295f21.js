webpackJsonp([16],{181:function(t,s,a){"use strict";function e(t){c||a(253)}Object.defineProperty(s,"__esModule",{value:!0});var i=a(204),n=a(254),c=!1,o=a(23),r=e,l=o(i.a,n.a,!1,r,"data-v-91ccc786",null);l.options.__file="src/module/smallBee/pages/erweima.vue",s.default=l.exports},204:function(t,s,a){"use strict";(function(t){var e=a(64),i=a.n(e),n=a(67),c=a(145);s.a={name:"erweima",data:function(){return{loading:1,name:"",phone:"",img:"",image:""}},components:{loading:c.a},methods:{jumpRouterEvr:function(t,s){this.$router.push({name:t,params:{}})},initia:function(){var s=this;t.ajax({url:n.a.MINE_INFO,headers:{token:this.$store.state.userInfo.apiToken},type:"GET",success:function(t){var a=t.data;console.log("个人信息："+i()(a)),200==a.code?(s.img=a.img,s.loading=0):s.$store.dispatch("changeTipHint",a.message)}})},getImg:function(){var s=this;t.ajax({url:"https://zxgybj.com/api/create-down-code",type:"GET",success:function(t){var a=t.data;console.log("二维码信息："+i()(a)),200==a.code?(s.image=a.EWMcodeUrl,s.loading=0):s.$store.dispatch("changeTipHint",a.message)}})}},activated:function(){this.initia(),this.getImg()}}}).call(s,a(38))},253:function(t,s){},254:function(t,s,a){"use strict";var e=function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"mine"},[a("div",{staticClass:"title"},[t._v("发展会员")]),t._v(" "),a("div",{staticClass:"title-c"}),t._v(" "),a("div",{staticClass:"trends-content"},[a("div",{staticClass:"middle-mgn"},[a("div",{staticClass:"trends-up"},[a("div",{staticClass:"trends-img"}),t._v(" "),a("div",{staticClass:"trends-info"},[a("div",{staticClass:"name-c"},[t._v(t._s("客户经理："+t.$store.state.oneselfInfo.name))]),t._v(" "),a("div",{staticClass:"time-c"},[a("div",{staticClass:"time-hot"},[t._v(t._s("联系方式："+t.$store.state.oneselfInfo.phone))])])])])])]),t._v(" "),t._m(0),t._v(" "),a("loading",{attrs:{loading:t.loading}})],1)},i=[function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"maBox"},[a("div",{staticClass:"imgBox"},[a("img",{attrs:{src:"http://zxgybj.com:8080/downloadApp.jpg",alt:""}})]),t._v(" "),a("div",{staticClass:"text"},[t._v("轻松借款")]),t._v(" "),a("div",{staticClass:"text left"},[t._v("极速办卡")]),t._v(" "),a("div",{staticClass:"text"},[t._v("评分查询")]),t._v(" "),a("div",{staticClass:"text right"},[t._v("金融资讯")])])}];e._withStripped=!0;var n={render:e,staticRenderFns:i};s.a=n}});