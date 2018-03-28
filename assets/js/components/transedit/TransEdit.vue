<template>
   <div @click.prevent="" class="transedit-container">
      <span class="editable"  @dblclick="startEditMode()">
         {{val}}
      </span>
      <div class="edit-window" v-if="editMode">
         <p>
            <textarea v-model="newVal" class="form-control"></textarea>
         </p>

         <button  @click="save" class="btn btn-success">Save</button>
         <button @click="cancel" class="btn btn-danger">Cancel</button>
         <span v-if="saving" class="saving">Saving</span>
         <span v-if="error" class="error">Something went wrong!</span>
      </div>
   </div>




</template>

<script>
    export default{
        props: ['tekey', 'teval'],
        data() {
            return {
               key : null,
               val: null,
               newVal: null,
               editMode: false,
               saving: false,
               error: false,
            };
         },
        methods:{
            startEditMode(){
                this.newVal = this.val;
                this.editMode = true;
            },
            save(){

                axios.post('/transedit/setkey', {key: this.key, val: this.newVal}).then((res) => {
                    this.val = this.newVal;
                    this.saving = false;
                    this.editMode = false;
                    this.error= false;
                }).catch((err) => {
                    this.error = true;
                    this.saving = false;
                });

            },
            cancel(){
                this.saving = false;
                this.error = false;
                this.newVal = this.val;
                this.editMode = false;

            }
        },
        mounted() {
            this.key = this.tekey;
            this.val = this.teval;
        }
    }
</script>
<style>
   .transedit-container{
      display: inline;
   }
   .editable{
      background-color: #FFFAC1;
      color: #000;

   }
   .edit-window {
      z-index: 400;
      position: absolute;
      background-color: #fefefe;
      margin: auto;
      padding: 10px;
      border: 1px solid #888;
      width: 500px;
      animation-duration: 0.4s
   }
   .saving:after {
      display: inline-block;
      animation: dotty steps(1,end) 1s infinite;
      content: '';
   }

   @keyframes dotty {
      0%   { content: ''; }
      25%  { content: '.'; }
      50%  { content: '..'; }
      75%  { content: '...'; }
      100% { content: ''; }
   }

   .error{
      color: #d00;
   }
</style>
