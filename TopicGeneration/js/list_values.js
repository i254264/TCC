var values = [{
    name: 'Jonny Strömberg',
    date: '20/03/2003',
    fulltext: `
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer porttitor feugiat nulla, vitae ultricies neque aliquam sit amet. Quisque vestibulum vitae purus eu feugiat. Phasellus ultrices lacus dolor, at elementum nisl tempor a. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque ut metus quis arcu laoreet vestibulum. Maecenas mattis venenatis diam at pulvinar.
    Fusce vitae nisl tellus. Vestibulum tempor dolor consectetur risus ultricies aliquet. Mauris at urna id orci gravida efficitur. Proin mollis facilisis lacus. Mauris fringilla aliquam quam, sed volutpat elit hendrerit quis. Proin id hendrerit eros, eu fringilla erat. In finibus commodo elementum.
    Sed elementum turpis sit amet mi semper, nec consequat nibh vehicula. Suspendisse rutrum, diam eu luctus facilisis, lacus risus egestas velit, sed fermentum lacus dolor a tellus. Mauris a porttitor urna. Mauris at varius velit, at ornare arcu. Sed tincidunt dui elit, nec ultrices tortor pharetra vitae. Praesent non urna nec neque faucibus dictum. Pellentesque lobortis dictum pellentesque. Morbi et convallis lorem, eget varius justo. Nulla neque metus, elementum at purus eget, pellentesque tristique sapien. Nam sed felis efficitur, ullamcorper leo ac, blandit risus. Etiam tempus enim eu aliquet mattis. Nam semper blandit dui id molestie. In ultricies enim elit, ac ultrices nunc imperdiet in. Nam fermentum, purus ac auctor luctus, lectus velit convallis tellus, non consequat elit metus viverra justo.
    Sed pretium sem purus, non sodales mauris congue id. Cras blandit sodales suscipit. Vestibulum mattis nunc in feugiat egestas. Sed et ligula non ipsum venenatis pellentesque. Nam semper tempus elit at eleifend. Phasellus eget orci vel metus pharetra imperdiet. Vivamus aliquam ipsum sit amet porta cursus. Curabitur ipsum erat, molestie id finibus vitae, auctor ut neque. Pellentesque venenatis urna a varius varius.
    Nunc tristique nunc ut dui consectetur dictum. Ut elementum mattis luctus. Integer non mi velit. Nunc arcu justo, commodo vel fringilla vitae, laoreet nec ligula. Proin elementum malesuada ultrices. Phasellus at quam eu orci laoreet dictum et eget mi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Mauris magna ipsum, gravida vitae mi quis, vestibulum semper dolor. Aenean consequat purus vel risus volutpat finibus. Vivamus sollicitudin tellus consectetur, dictum velit et, rhoncus ex. Pellentesque placerat elit sed tellus tempus aliquam. Pellentesque nec libero bibendum, efficitur est id, finibus ipsum. Maecenas at placerat erat.
    ` 
},
{
    name: 'Jonas Arnklint',
    date: '20/03/2003',
    fulltext: `a`
},
{
    name: 'Jonas Arnklint',
    date: '20/03/2003',
    fulltext: `e`
},
{
    name: 'Jonas Arnklint',
    date: '20/03/2003',
    fulltext: `i`
},

{
    name: 'Martina Elm',
    date: '20/03/2003',
    fulltext: `o`
}];
var paginationOptions = {
    name: "pagination",
    paginationClass: "pagination"
};
var listOptions = {
    valueNames: ['name', 'date', 'fulltext'],
    item: '<li><p class="name"></p> <p class="date"></p> <p class="fulltext"></p></li>',
    page: 5,
    pagination: true,
};

var listObj = new List('all-files', listOptions, values);

$('.search').on('click', function(){
    $('#search-icon').addClass("search-icon-active");
});
$('.next').on('click', function(){
    $('.pagination .active').next().trigger('click');
});

$('.prev').on('click', function(){
    $('.pagination .active').prev().trigger('click');
});

listObj.add({
    name: "Gustaf Lindqvist",
    date: '20/03/2003',
    fulltext: `u`
});

$(document).on('click', '.list li', function() {

    $('.list li').css('background-color', '#d6d4d4');
    $(this).css('background-color', '#5279c04d');
    // Captura o conteúdo do atributo 'fulltext' do elemento clicado
    let fulltextValue = $(this).find('.fulltext').text();

    
    $('.alert').fadeOut(200, function() {
        setTimeout(function () {
            $("#full-file").css("display", "flex");
            $("#full-file").addClass("is-open");
            $('#full-file p').text(fulltextValue);
        }, 350);
    });

    setTimeout(function () {
        $('#full-file p').text(fulltextValue);
    }, 100);
});