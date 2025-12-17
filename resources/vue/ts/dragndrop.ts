'use strict';

import * as $ from 'jquery';
import Sortable from 'sortablejs';
import { environment } from './environments/environment';
import axios, { Axios } from  'axios-observable';
import { catchError, throwError } from 'rxjs';

let sortableInstance: Sortable | null = null;

const csrfToken: HTMLElement = document.head.querySelector('meta[name="csrf-token"]') as HTMLElement;
const apiToken: HTMLElement = document.head.querySelector('meta[name="api-token"]') as HTMLElement;

axios.defaults.headers.common['Content-Type'] = "application/json";
axios.defaults.headers.common['Accept'] = "application/json";
axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
axios.defaults.headers.common['Authorization'] = 'Bearer ' + apiToken.getAttribute('content');

function dragndrop(): void {

    function after_drop(): void {
        var alist: number[] = [];

        $("#page-list-table tbody tr").each(function (iter: number) {
            var $this: JQuery<HTMLElement> = $(this);
            var pageId: string = $this.find('td').eq(1).html().split(" ")[0];

            alist[iter] = Number(pageId);
        });

        axios.put(environment.REST_API_BASE+"/pages/reorder", {
            order: alist
        })
        .pipe(
            catchError((error: any) => {
                console.error(error);
                return throwError(error);
            })
        )
        .subscribe((response: any) => {
            console.log(response);
        });

    }

    const tbody = document.querySelector('#page-list-table tbody') as HTMLElement;
    if (tbody) {
        sortableInstance = Sortable.create(tbody, {
            handle: '.fa-arrows-v',
            onEnd: function (evt: Sortable.SortableEvent) {
                renumber_table('#page-list-table');
                after_drop();
            }
        });
    }

    $('table').on('click', '.btn-delete', function (this: HTMLElement) {
        var tableID: string = '#' + ($(this).closest('table').attr('id') || '');
        var r: boolean = confirm('Delete this item?');
        if (r) {
            $(this).closest('tr').remove();
            renumber_table(tableID);
        }
    });
}

function renumber_table(tableID: string): void {
    $(tableID + " tr").each(function (this: HTMLElement) {
        var count: number = $(this).parent().children().index($(this)) + 1;
        $(this).find('.priority').html(count.toString());
    });
}

export default function dragndroporder(): void {
    $('#orderer').toggleClass('btn-default');
    $('#orderer').toggleClass('btn-success');

    if ($('#page-list-table').hasClass('order-active')) {
        $('.torder').remove();
        $('#page-list-table').removeClass('order-active');
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }
    } else {

        $('table').find('tr').each(function (this: HTMLElement) {
            $(this).find('th').eq(0).before("<th class='col-md-1 torder'>Reorder</th>");
            $(this).find('td').eq(0).before("<td class='torder'><i class='well well-sm fa fa-arrows-v' style='border-radius:3px;cursor:grab;font-size:20px;' aria-hidden='true'></i></td>");
        });

        $('#page-list-table').addClass('order-active');
        dragndrop();
    }
}

window.dragndroporder = dragndroporder;