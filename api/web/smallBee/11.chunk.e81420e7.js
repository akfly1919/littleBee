webpackJsonp([11],{171:function(t,i,s){"use strict";function a(t){o||s(226)}Object.defineProperty(i,"__esModule",{value:!0});var e=s(193),n=s(227),o=!1,c=s(23),l=a,r=c(e.a,n.a,!1,l,"data-v-87a35100",null);r.options.__file="src/module/smallBee/pages/client.vue",i.default=r.exports},183:function(t,i,s){t.exports=s.p+"img/df81ee1f.telephone.png"},193:function(t,i,s){"use strict";(function(t){var a=s(64),e=s.n(a),n=s(67),o=s(145);i.a={name:"client",data:function(){return{loading:1,apiToken:"",info:[]}},components:{loading:o.a},methods:{initia:function(){var i=this;t.ajax({url:n.a.CLIENT_INFO,headers:{token:this.$store.state.userInfo.apiToken},data:{limit:100,offset:0},type:"GET",success:function(t){var s=t.data;console.log("我的客户："+e()(s)),200==s.code?(i.info=s.myClient,i.loading=0):i.$store.dispatch("changeTipHint",t.message)}})}},activated:function(){this.initia()}}}).call(i,s(38))},226:function(t,i){},227:function(t,i,s){"use strict";var a=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("div",{staticClass:"mine"},[a("div",{staticClass:"title"},[t._v("我的客户")]),t._v(" "),a("div",{staticClass:"title-c"}),t._v(" "),t._l(t.info,function(i,e){return a("div",{staticClass:"trends-content"},[a("div",{staticClass:"middle-mgn"},[a("div",{staticClass:"trends-up"},[a("div",{staticClass:"imgBox"},[i.img?a("img",{staticClass:"trends-img",attrs:{src:i.img}}):t._e()]),t._v(" "),a("div",{staticClass:"trends-info"},[a("div",{staticClass:"name-c"},[t._v(t._s("姓名："+i.name))]),t._v(" "),a("div",{staticClass:"time-c"},[a("div",{staticClass:"time-hot"},[t._v(t._s("电话："+i.phone))])]),t._v(" "),a("div",{staticClass:"name-c"},[t._v(t._s(i.pro?"产品："+i.pro:"产品：无"))])])]),t._v(" "),a("div",{staticClass:"toPhone"},[a("img",{attrs:{src:s(183),alt:""}}),t._v(" "),a("a",{attrs:{href:"tel:"+i.phone}})])])])}),t._v(" "),t.loading?t._e():a("div",{staticClass:"no-other"},[t._v("--对不起，没有更多客户了--")]),t._v(" "),a("loading",{attrs:{loading:t.loading}})],2)},e=[];a._withStripped=!0;var n={render:a,staticRenderFns:e};i.a=n}});