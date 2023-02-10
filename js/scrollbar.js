class Scrollbar {

	constructor (id){
        this.id = document.getElementById(id);
        this.height = this.id.offsetHeight;
        this.width = this.id.offsetWidth;
        this.top = 0;
        document.getElementById("scroll_up").addEventListener('click', this.scroll_up);
	}

    set_scrollbar() {
        let monitor_height = document.getElementById("scrolleable_cont").offsetHeight;
        let cont_height = document.getElementById("cont_monitor").offsetHeight;
        let relacion = monitor_height / cont_height;
        let scrollbar_puntero = this.height * relacion;
        if(relacion <= 1){
            document.getElementById("puntero_scrollbar").style.height = scrollbar_puntero + 'px';
        } else {
            document.getElementById("puntero_scrollbar").style.height = '0px';
        }
    }

    scroll_up(){
        console.log(this.top);
        this.top = parseInt(this.top) - 100;
        document.getElementById("cont_monitor").offsetTop = this.arriba + "px";
    }

};
