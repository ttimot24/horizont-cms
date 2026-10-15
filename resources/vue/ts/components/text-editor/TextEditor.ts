import { defineComponent } from 'vue';
import { environment } from '../../environments/environment';

export default defineComponent({
    name: 'text-editor',
    props: {
        name: {
            type: String,
            default: '',
            required: true
        },
        data: { 
            type: String
        },
        language: {
            type: String,
            default: 'en'
        },
        filebrowserbrowseurl: {
            type: String
        },
        filebrowseruploadurl: {
            type: String
        },
        height: {
            type: Number,
            default: 750
        }
    },
    mounted() {
        const editorInterval = setInterval(() => {

            const editorInstance = (window as any).CKEDITOR?.instances?.['editor1'];
            if (editorInstance) {
                clearInterval(editorInterval);
    
                editorInstance.on('fileUploadRequest', (evt: any) => {
                    const fileLoader = evt.data.fileLoader;
                    const xhr = fileLoader.xhr;
                    const formData = new FormData();
                
                    formData.append('up_file[]', fileLoader.file, fileLoader.file.name);

                    evt.data.requestData = formData;

                    xhr.setRequestHeader('Authorization', this.http.defaults.headers.common['Authorization']);
                    xhr.setRequestHeader('Accept', this.http.defaults.headers.common['Accept']);
                
                    evt.stop();
                    xhr.send(formData);
                });
            }
        }, 100);
    },
    data: function() {
        return {
            content: this.data,
            editorUrl: 'https://cdn.ckeditor.com/'+environment.CKEDITOR_VERSION+'/full/ckeditor.js',
            editorConfig: {
                versionCheck: false,
                language: this.language,
                skin: 'moono-lisa',
                filebrowserUploadMethod: 'xhr',
                removeButtons: 'NewPage,Save,Font,FontSize,Styles,Flash,Print,Language,Templates,PageBreak',
                height: this.height,
                filebrowserBrowseUrl: this.filebrowserbrowseurl,
                filebrowserUploadUrl: this.filebrowseruploadurl,
                fullPage: true,
                allowedContent: true,
                extraPlugins: 'uploadimage',
                uploadImage_supportedTypes: /image\/(jpeg|png|gif|bmp|webp|svg)/
            }
        };
    },
    methods:{
        output: function(data: string){
            this.$emit('output', data);
        }
    }
});
