<template lang="pug">
  div.page_post_register(v-if="post")
    .container
      .row
        .col-sm-12

          ol.breadcrumb
            li.breadcrumb-item 
              router-link(to="/manage/post") 新聞管理
            li.breadcrumb-item.active 新聞編輯
          h2(v-if="post" ) 編輯- {{ strip_tags(post.title) }}
            
            button.btn.btn-danger.pull-right(@click="deletePost") Delete
            button.btn.btn-primary.pull-right(@click="updatePost") Save
          h2(v-else) 新文章 - {{ strip_tags(post.title) }}
            button.btn.btn-primary.pull-right(@click="updatePost") Save
          hr

        .col-sm-4
          .panel.panel-primary
            .panel-heading 基本資訊
            .panel-body
              //.form-group
                labal.col-sm-3 Type
                .col-sm-9
                  select.form-control(v-if="post"  v-model="post.type")
                    option(v-for="op in activityTypeOptions", :value="op.value") {{op.tag}}
                br
                br
              .form-group
                labal.col-sm-3 標題
                .col-sm-9
                  input.form-control(v-if="post" v-model="post.title")
                br
                br
              .form-group
                labal.col-sm-3 日期
                .col-sm-9
                  input.form-control(v-if="post" v-model="post.date", placeholder="2017-xx-xx")
                br
                br
              .form-group
                labal.col-sm-3 類別
                .col-sm-9
                  input.form-control(v-model="post.cata")
                br
                br
              .form-group
                labal.col-sm-3 Tag
                .col-sm-9
                  //- input.form-control(v-model="post.tag")
                  .form-inline
                    input.form-control(v-model="newTag")
                    .btn.btn-primary(@click="post.tag.push(newTag);newTag=''") + 
                  .btn.btn-default(v-for="(tag,tagid) in post.tag") {{tag}}
                    span(@click="post.tag.splice(tagid,1)") &nbsp;-
                  
                br
                br
                br
              
              .form-group
                labal.col-sm-3 封面
                .col-sm-9
                  input.form-control(v-model="post.cover")
                  img(:src="post.cover", style="width: 100%")
                  default_pic_selector(@select_pic="select_pic_cover")
                br
                br
                br

              .form-group
                labal.col-sm-3 顯示
                .col-sm-9
                  input.form-control(type="checkbox" v-model="post.show")
                br
                br

              .form-group
                labal.col-sm-3 置頂
                .col-sm-9
                  input.form-control(type="checkbox" v-model="post.stick")
                br
                br

        .col-sm-8
          .panel.panel-default
            .panel-heading 短描述
            .panel-body
              .form-group
                .col-sm-12
                  VueEditor.ve(:id ="'description'", v-model="post.description" ,
                    :useCustomImageHandler="true",
                    @imageAdded="handleImageAdded",
                    :placeholder="strip_tags(post.content).slice(0,200)" )
                  br
                  br
          .panel.panel-default
            .panel-heading 內容
            .panel-body
              .form-group
                .col-sm-12
                  VueEditor.ve(:id ="'content'", v-model="post.content" ,
                    :useCustomImageHandler="true",
                    :editorToolbar="customToolbar",
                    @imageAdded="handleImageAdded" )
                  br
                  br
              //.form-group
                labal.col-sm-3 註冊資訊
                .col-sm-9
                  VueEditor.ve(:id ="'register_info'", v-model="post.register_info")
                  br
                  br
          
          </template>

</template>

<script>
import { VueEditor } from 'vue2-editor'
import {mapState} from 'vuex'
import axios from 'axios'
import default_pic_selector from '../default_pic_selector.vue'
//import ActivityInfoRow from './ActivityInfoRow'
import _ from 'lodash'
export default {
  props: ["post_id"],
  data(){
    return {
      order: true,
      post: {
        show: 1,
        title: "新文章",
        date: (new Date()).toLocaleString().split(" ")[0].replace(/\//g,"-"),
        description: "",
        content: ""
      },
      customToolbar: [
          ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
          
          ['my-thing'],
          ['blockquote', 'code-block','image','video','link'],

          // [{ 'header': 1 }, { 'header': 2 }],               // custom button values
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
          [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
          [{ 'direction': 'rtl' }],                         // text direction

          // [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

          [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
          [{ 'font': [] }],
          [{ 'align': [] }],

          ['clean']                                         // remove formatting button
      ],
    }
  },
  mounted() {
      console.log('Component mounted.')
      var a = this.nowpost
  },
  computed:{
    ...mapState(['user','activities','posts']),
    nowpost(){
      let result = this.posts.find(o=>o.id==this.post_id)
      if (result ){
        this.post = result
      }
      return result
    }
  },
  components:{
    VueEditor,default_pic_selector
  },
  methods: {
    setPost(post){
      // post.tag = JSON.parse(post.tag)
      // post.speaker = JSON.parse(post.speaker)
      // if (!post.album){
      //   post.album=Array.from({length: 4},()=>({image: "",caption: ""}))
      // }else{
      //   post.album = JSON.parse(post.album)
      // }
      // // console.log(post.album)
      this.post = post
    },
    deletePost(){
      this.$confirm("你確定要刪除文章嗎？").then(()=>{
        axios.post("/api/post/"+this.post_id,{
          _method: "DELETE"
        }).then((res)=>{
          this.$message.success("刪除成功!")
          this.$router.push('/manage/post')
          this.$store.dispatch("loadPosts")
        })
      })

    },
    select_pic_cover(obj){
      this.post.cover = obj.url
    },
    updatePost(){
      if (this.$route.path=="/manage/post/new"){
        axios.post("/api/post",{
          _method: "POST",
          ...this.post
          
        }).then((res)=>{
          this.setPost(res.data)
          this.$message.success("建立成功!")
        })
      }else{
        axios.post("/api/post/"+this.post_id,{
          _method: "PATCH",
          ...this.post
          
        }).then((res)=>{
          this.setPost(res.data)
          this.$message.success("儲存成功!")
        })
      }
      

    }
  }
}

</script>


<style lang="sass?indentedSyntax">
</style>
