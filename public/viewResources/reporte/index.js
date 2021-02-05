'use strict';

function openModal(element)
{
	eval($(element).find('a').data('openmodal'));
}