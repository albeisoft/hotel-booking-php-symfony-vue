
<template>    
    <Button type="button" label="Close" icon="pi pi-times" @click="closeDialog({ buttonType: 'Close' })" text></Button>
    <Button type="button" label="Save" icon="pi pi-check" @click="closeDialog({ buttonType: 'Save' })" autofocus></Button>     
</template>

<script>
export default {
    inject: ['dialogRef'],
    methods: {
        closeDialog(options) {
            const buttonType = options.buttonType;
            
            console.log('from modal footer - buttonType # ' + JSON.stringify(buttonType, null,2));
            //console.log('this.dialogRef # ' + JSON.stringify(this.dialogRef, null, 2));
            console.log('from modal footer - this.dialogRef.data.dataId # ' + JSON.stringify(this.dialogRef.data.dataId, null, 2));
            
            this.dialogRef.close(options);
        }
    }
};
</script>