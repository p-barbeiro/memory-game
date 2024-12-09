<template>
	<div class="container  px-4 py-6 bg-white md:flex md:items-center md:justify-between">
	  <span class="flex text-sm text-gray-500 justify-center text-center">IPLeiria | Distributed Applications Development | 2024-2025 </span>
	  <div class="flex mt-4 justify-center md:mt-0 space-x-2 rtl:space-x-reverse">
		<a id="DA" href="https://www.linkedin.com/in/diogo-abegao/" class="text-gray-400 hover:text-indigo-950 text-sm p-1 font-semibold" @mouseover="showFullName('DA')" @mouseleave="hideFullName('DA')"> DA </a>
		<div class="text-gray-400 text-sm p-1">|</div>
		<a id="JP" href="https://www.linkedin.com/in/joao-parreira-dev/" class="text-gray-400 hover:text-indigo-950 text-sm p-1 font-semibold" @mouseover="showFullName('JP')" @mouseleave="hideFullName('JP')"> JP </a>
		<div class="text-gray-400 text-sm p-1">|</div>
		<a id="MO" href="https://www.linkedin.com/in/marcelo-oliveira-89602b212/" class="text-gray-400 hover:text-indigo-950 text-sm p-1 font-semibold" @mouseover="showFullName('MO')" @mouseleave="hideFullName('MO')"> MO </a>
		<div class="text-gray-400 text-sm p-1">|</div>
		<a id="PB" href="https://www.linkedin.com/in/pedrobarbeiro/" class="text-gray-400 hover:text-indigo-950 text-sm p-1 font-semibold" @mouseover="showFullName('PB')" @mouseleave="hideFullName('PB')"> PB </a>
	  </div>
	</div>
  </template>
  
  <script setup>
  const names = {
	DA: 'Diogo Abegão',
	JP: 'João Parreira',
	MO: 'Marcelo Oliveira',
	PB: 'Pedro Barbeiro'
  };
  
  const showFullName = (id) => {
	document.getElementById(id).textContent = names[id];
  };
  
  const hideFullName = (id) => {
	document.getElementById(id).textContent = id;
  };
  </script>