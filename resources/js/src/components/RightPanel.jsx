import React, { memo } from 'react'
import { Blogs } from '.'

function RightPanel() {
  return (
    <div className='md:col-span-9'>
      <Blogs />
    </div>
  )
}

export default memo(RightPanel)