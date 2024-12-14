import { cva } from 'class-variance-authority';

export { default as Badge } from './Badge.vue';

export const badgeVariants = cva(
  'inline-flex items-center place-self-center rounded-md h-8 w-24 mb-4 md:mb-0 flex justify-center px-2.5 py-0.5 text-xs font-normal transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
  {
    variants: {
      variant: {
        default:
          'border border-gray-100 dark:border-gray-700 text-gray-800 dark:text-gray-100 bg-transparent',
        gray:
          'bg-gray-100 border-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
        indigo:
          'bg-indigo-200 text-indigo-700',
        yellow:
          'bg-yellow-200 text-yellow-500',
        red:
          'bg-red-100 text-red-500',
        green:
          'bg-green-200 text-green-500',
        blue:
          'bg-blue-200 text-blue-500',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
);
