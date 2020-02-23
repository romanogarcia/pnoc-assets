(function($) {
  'use strict';
  $(function() {
    var body = $('body');
    var contentWrapper = $('.content-wrapper');
    var scroller = $('.container-scroller');
    var footer = $('.footer');
    var sidebar = $('.sidebar');

    //Add active class to nav-link based on url dynamically
    //Active class can be hard coded directly in html file also as required

    function addActiveClass(element) {
      if (current === "") {
        //for root url
        if (element.attr('href').indexOf("index.html") !== -1) {
          element.parents('.nav-item').last().addClass('active');
          if (element.parents('.sub-menu').length) {
            element.closest('.collapse').addClass('show');
            element.addClass('active');
          }
        }
      } else {
        //for other url
        if (element.attr('href').indexOf(current) !== -1) {
          element.parents('.nav-item').last().addClass('active');
          if (element.parents('.sub-menu').length) {
            element.closest('.collapse').addClass('show');
            element.addClass('active');
          }
          if (element.parents('.submenu-item').length) {
            element.addClass('active');
          }
        }
      }
    }

    var current = location.pathname.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');
    $('.nav li a', sidebar).each(function() {
      var $this = $(this);
      addActiveClass($this);
    })

    //Close other submenu in sidebar on opening any

    sidebar.on('show.bs.collapse', '.collapse', function() {
      sidebar.find('.collapse.show').collapse('hide');
    });


    //Change sidebar

    $('[data-toggle="minimize"]').on("click", function() {
      body.toggleClass('sidebar-icon-only');
    });

    //checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');

    // Remove pro banner on close
    // document.querySelector('#bannerClose').addEventListener('click',function() {
    //   document.querySelector('#proBanner').classList.add('d-none');
    // });

    //<- Start of RealTime Date/Clock->
    
    var currenttime = $('#server-current_date_container_key').data('server_time'); //From PHP method of getting server date
    var montharray  = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
    var week_days   = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    var serverdate  = new Date(currenttime);

    function padlength(what){
      var output=(what.toString().length==1)? "0"+what : what;
      return output;
    }
    function displaytime(){
      serverdate.setSeconds(serverdate.getSeconds()+1);
      var datestring  = montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear();
      var timestring  = padlength(week_days[serverdate.getDay()-1]+" "+serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds());
      var time_container = $("#top_nav-current_realtime_date");
      
      if(window.matchMedia("(max-width: 992px)").matches){
        time_container.css({
          'font-size'   : '11px',
          'white-space' : 'normal',
          'display'     : 'block',
        });
      }else{
        time_container.css({
          'font-size'   : '14px',
          'white-space' : 'normal',
          'display'     : 'block',
        });
      }
      time_container.html(datestring+" "+timestring);
    }
    window.setInterval(function (){
      displaytime();
    }, 1000);

    //<-/ End of RealTime Date/Clock->

  });
})(jQuery);