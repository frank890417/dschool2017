<template lang="pug">
  .page.pageActivityList
    section.sectionHero.blue
      img.coverGraphic(src="/static/img/hero_activity_cover.svg", :style="{transform: `translateY(${scrollTop/3}px)`}")
            
    section.sectionList.theme.blue
      .container
        .row
          .col-sm-12
            //- h1 學院活動
        .row
          .col-sm-12
            #calendar
        .row
          .col-sm-12
            router-link.btn.btn-primary.pull-right(v-if="user && user.admingroup=='root'",
                                        to="/manage/activity/new") + 新增活動
            .btn.btn-default(v-if="order", @click="order=!order") 新 → 舊
            .btn.btn-default(v-else, @click="order=!order") 舊 → 新

          .col-sm-12
            .monthGroup(v-for="monthSet in chunkedList.slice(0,showCount)")
              h2.month {{monthSet.time.slice(0,4)+' / '+monthSet.time.slice(4)}}月
              ul
                li(v-for="(activity,aid) in monthSet.events",
                  :style="{opacity: activity.mode=='draft'?0.5:1}" )
                  ActivityInfoRow(
                    :event_id="activity.id", 
                    :key="activity.id",title="查看資訊",
                    :class="'delay_ani_'+(3+aid*3)")
          .col-sm-12
            .btn.w100.trans-white.mt-5(@click="showCount+=2",
                v-if="chunkedList.length>showCount") 顯示更多活動

</template>

<script>
import {mapState} from 'vuex'
import ActivityInfoRow from './ActivityInfoRow'
// import { FullCalendar } from 'vue-full-calendar'
import _ from 'lodash'
export default {
    data(){
      return {
        order: true,
        showCount: 3
      }
    },
    mounted() {
        console.log('Component mounted.')
       $('#calendar').fullCalendar({
       })
    },
    computed:{
      ...mapState({
        auth: 'auth',
        user: state=>state.auth.user,
        activities: 'activities',
        'scrollTop': 'scrollTop'
      }),
      is_admin(){
        return this.auth.user && this.auth.user.admingroup=='root' 
      },
      OrderedList(){
        let result = this.activities.filter(o=>o.mode=="published" || this.is_admin ).slice().sort((a,b)=>new Date(a.time)>new Date(b.time) )
        
        if (this.order){
          result=result.reverse()
        }
        // console.log(result.map(o=>o.time))
        return result
      },
      chunkedList(){
        //依照年分跟月份分群
        let result =  _.groupBy(
          this.OrderedList,event=>
            (new Date(event.time.replace(/-/g, "/") )).getFullYear()*100+ ((new Date(event.time.replace(/-/g, "/") )).getMonth()+1)
        )

        //轉換成陣列進行排序
        let resultArray = []
        let keys = Object.keys(result)
        if (keys){
          keys.forEach(key => {
                  console.log(key)
                  resultArray.push( {
                  'time':key, 
                  'events':result[key]
                  })
          })
          
        }
        if (this.order){
          resultArray=resultArray.reverse()
        }

        return resultArray
      }
    },
    components:{
      ActivityInfoRow,
      // FullCalendar
    },
    methods: {
      
    },
    watch:{
      activities(){
        // $('#calendar').fullCalendar('renderEvent', {
        //     id: 'eventGroup1',
        //     title: '活動1',
        //     start: moment().add(3, 'days').format('YYYY-MM-DD'),
        //     textColor: 'black',
        //     color: 'beige'
        // });
      }
    }
}
</script>


<style lang="sass?indentedSyntax">
</style>
