webpackJsonp([3],{155:function(e,t,s){"use strict";function i(e){n||s(171)}Object.defineProperty(t,"__esModule",{value:!0});var r=s(160),a=s(172),n=!1,o=s(30),d=i,c=o(r.a,a.a,!1,d,"data-v-1f106a76",null);c.options.__file="src/module/cThird/pages/add.vue",t.default=c.exports},160:function(e,t,s){"use strict";(function(e){var i=s(88),r=s.n(i);t.a={name:"register",data:function(){return{userId:this.$route.query.id,userName:"",regPhone:"",vertionCodeText:"获取验证码",regProving:"",userToken:"",time:60,sendFlag:0,certificatesProving:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/}},methods:{retrieveFrom:function(){var t=this;return this.userName&&this.regPhone&&this.regProving?11==this.regPhone.length&&/^1[34578]\d{9}$/.test(this.regPhone)?this.certificatesProving.test(this.userId)?void e.ajax({url:"https://zxgybj.com/api/save-client",data:{identity:this.userId,phone:this.regPhone,name:this.userName,msgCode:this.regProving,shareID:this.$store.state.pageInfo.shareID,sjTeamID:this.$store.state.pageInfo.sjTeamID,randomStr:this.userToken},type:"post",success:function(e){var s=e.data;console.log("客户登记页："+r()(e)),200==s.code?t.$router.push("/view"):t.$store.dispatch("changeTipHint",s.message)}}):void this.$store.dispatch("changeTipHint","身份证号码不合法！"):void this.$store.dispatch("changeTipHint","手机号码不合法"):void this.$store.dispatch("changeTipHint","信息填写不全")},getVertionCode:function(){var t=this;return this.sendFlag?void this.$store.dispatch("changeTipHint","已发送哦~"):this.regPhone?void e.ajax({url:"https://zxgybj.com/api/get-msg-code",headers:{},data:{phone:this.regPhone},type:"post",success:function(e){var s=e.data;if(200==s.code){console.log("获取验证码"+r()(s)),t.$store.dispatch("changeTipHint","已发送"),t.userToken=s.randomStr;var i=setInterval(function(){t.time--,t.sendFlag=t.time,t.time<1&&(clearInterval(i),t.time=60)},1e3);t.sendFlag=t.time}else t.$store.dispatch("changeTipHint",s.message)}}):void this.$store.dispatch("changeTipHint","手机号不能为空")}}}}).call(t,s(87))},171:function(e,t){},172:function(e,t,s){"use strict";var i=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"register"},[s("div",{staticClass:"title"},[e._v("客户登记信息")]),e._v(" "),s("div",{staticClass:"register-content"},[s("input",{directives:[{name:"model",rawName:"v-model",value:e.userId,expression:"userId"}],staticClass:"user-id",attrs:{placeholder:"有效身份证号",disabled:"disabled",type:"number"},domProps:{value:e.userId},on:{input:function(t){t.target.composing||(e.userId=t.target.value)}}}),e._v(" "),s("input",{directives:[{name:"model",rawName:"v-model",value:e.userName,expression:"userName"}],staticClass:"user-name",attrs:{placeholder:"真实姓名",type:"text",maxlength:"12"},domProps:{value:e.userName},on:{input:function(t){t.target.composing||(e.userName=t.target.value)}}}),e._v(" "),s("input",{directives:[{name:"model",rawName:"v-model",value:e.regPhone,expression:"regPhone"}],staticClass:"reg-phone",attrs:{placeholder:"请输入手机号",disabled:!!e.sendFlag&&"disabled",type:"number",maxlength:"11"},domProps:{value:e.regPhone},on:{input:function(t){t.target.composing||(e.regPhone=t.target.value)}}}),e._v(" "),s("div",{staticClass:"reg-proving"},[s("input",{directives:[{name:"model",rawName:"v-model",value:e.regProving,expression:"regProving"}],attrs:{placeholder:"请输入验证码",type:"number",maxlength:"6"},domProps:{value:e.regProving},on:{input:function(t){t.target.composing||(e.regProving=t.target.value)}}}),e._v(" "),s("div",{staticClass:"get-proving",on:{click:e.getVertionCode}},[e._v(e._s(e.sendFlag>0?e.sendFlag+"s":e.vertionCodeText)+"\n      ")])]),e._v(" "),s("button",{staticClass:"sub-from",attrs:{type:"button"},on:{click:e.retrieveFrom}},[e._v("提交信息")])])])},r=[];i._withStripped=!0;var a={render:i,staticRenderFns:r};t.a=a}});